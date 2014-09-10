<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 27.08.2014
 * Time: 15:40
 */


namespace App\DataImport;


use App\Helpers\FSHelper;
use App\Model\Category;
use App\Pixie;

/**
 * Imports categories and products from directory.
 * @package App\DataImport
 */
class CategoryProductImporter
{
    protected $pixie;

    /**
     * @var null|Category
     */
    protected $rootCategory = null;

    protected $result = [];

    protected $lastCategoryLevel;

    public function __construct(Pixie $pixie)
    {
        $this->pixie = $pixie;
    }

    /**
     * Main import function. Clears DB and then imports categories and products from path.
     * @param string $path Path to directory with categories and products.
     * @param int $categoryLevels Number of category levels.
     * @return array Debug and statistics information.
     * @throws \Exception If path is incorrect.
     */
    public function import($path, $categoryLevels = 2)
    {
        $this->lastCategoryLevel = $categoryLevels - 1;

        if (!file_exists($path) || !is_dir($path) || !is_readable($path)) {
            throw new \Exception('Product and category path is not accessible');
        }

        $this->result = [];
        $this->clearDatabase();
        $this->importCategory($this->getRoot(), $path);

        return $this->result;
    }

    /**
     * Imports category in certain level.
     * @param Category $parent
     * @param $path
     * @param int $depth
     */
    public function importCategory(Category $parent, $path, $depth = 0)
    {
        $dirIterator = new \DirectoryIterator($path);
        /** @var \SplFileInfo $fileInfo */
        foreach ($dirIterator as $fileInfo) {
            if (!$fileInfo->isDir() || !$fileInfo->isReadable() || in_array($fileInfo->getFilename(), ['.', '..'])) {
                continue;
            }

            $category = $this->createCategory([
                'name' => $fileInfo->getFilename(),
                'enabled' => 1,
                'parent' => $parent->id()
            ], $parent);
            $this->result['category_num']++;

            if ($depth < $this->lastCategoryLevel) {
                $this->importCategory($category, $fileInfo->getPathname(), $depth + 1);
            } else {
                $this->importProducts($category, $fileInfo->getPathname());
            }
        }
    }

    /**
     * Imports all products from a directory.
     * @param Category $category
     * @param $path
     */
    public function importProducts(Category $category, $path)
    {
        $dirIterator = new \DirectoryIterator($path);
        /** @var \SplFileInfo $fileInfo */
        foreach ($dirIterator as $fileInfo) {
            if (!$fileInfo->isDir() || !$fileInfo->isReadable() || in_array($fileInfo->getFilename(), ['.', '..'])) {
                continue;
            }

            $this->importProduct($fileInfo->getFilename(), $category, $fileInfo->getPathname());
        }
    }

    /**
     * Imports single product from its directory.
     * Directory must contain a text file.
     * @param $name
     * @param Category $category
     * @param $path
     */
    public function importProduct($name, Category $category, $path)
    {
        $dirIterator = new \DirectoryIterator($path);
        $text = '';
        $imageBig = '';
        $imageBigNewPath = '';
        $imageBigOldPath = '';
        $imageSmall = '';
        $imageSmallNewPath = '';
        $imageSmallOldPath = '';

        $targetDir = realpath(__DIR__.'/../../../web/products_pictures/') . DIRECTORY_SEPARATOR;

        /** @var \SplFileInfo $fileInfo */
        foreach ($dirIterator as $fileInfo) {
            if (!$fileInfo->isFile() || !$fileInfo->isReadable() || in_array($fileInfo->getFilename(), ['.', '..'])) {
                continue;
            }

            if ($fileInfo->getExtension() == 'txt') {
                $text = file_get_contents($fileInfo->getPathname());
            }

            $ext = strtolower($fileInfo->getExtension());
            $baseName = strtolower($fileInfo->getFilename());

            if (in_array($ext, ['jpeg', 'jpg', 'gif', 'png'])) {
                if ($this->stringContains($baseName, ['small', 'little', 'tiny'])) {
                    $imageSmall = $this->generateFilename($name)
                        . '_small_' . substr(sha1($fileInfo->getPathname()), 0, 6) . '.' . $fileInfo->getExtension();
                    $imageSmallOldPath = $fileInfo->getPathname();
                    $imageSmallNewPath = $targetDir . $imageSmall;

                } else if ($this->stringContains($baseName, ['big', 'large', 'huge'])) {
                    $imageBig = $this->generateFilename($name)
                        . '_big_' . substr(sha1($fileInfo->getPathname()), 0, 6) . '.' . $fileInfo->getExtension();
                    $imageBigOldPath = $fileInfo->getPathname();
                    $imageBigNewPath = $targetDir . $imageBig;
                }
            }
        }

        if (!$text) {
            return;
        }

        $data = [];
        $blocks = preg_split('/((\{(.+)\})+)/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $data['name'] = $blocks[0];
        $data['description'] = $blocks[1];
        $data['price'] = $blocks[2];

        $product = $this->pixie->orm->get('Product');
        $product->values([
            'name' => $data['name'] ?: $name,
            'categoryID' => $category->categoryID,
            'description' => $data['description'],
            'picture' => $imageSmall,
            'big_picture' => $imageBig,
            'Price' => preg_replace('/\\$/', '', $data['price'])
        ]);

        $product->save();

        $this->copyFile($imageSmallOldPath, $imageSmallNewPath);
        $this->copyFile($imageBigOldPath, $imageBigNewPath);

        $this->result['product_num']++;
    }

    public static function create(Pixie $pixie)
    {
        return new self($pixie);
    }

    /**
     * Get or create root category.
     * @return null|Category
     */
    protected function getRoot()
    {
        if (!$this->rootCategory) {
            $this->rootCategory = $this->createCategory([
                'name' => '0_ROOT',
                'enabled' => 1
            ]);
            $this->rootCategory->refresh();
            $this->rootCategory->parent = 0;
            $this->rootCategory->save();
        }

        return $this->rootCategory;
    }

    protected function createCategory(array $values = [], Category $parent = null)
    {
        /** @var Category $category */
        $category = $this->pixie->orm->get('Category');
        $category->values($values);
        $category->nested->prepare_append($parent);
        $category->save();
        return $category;
    }

    protected function clearDatabase()
    {
        $this->pixie->db->query('delete')->table('tbl_orders')->execute();
        $this->pixie->db->query('delete')->table('tbl_products')->execute();
        $this->pixie->db->query('delete')->table('tbl_categories')->execute();
        $this->pixie->db->get()->execute("alter table tbl_products AUTO_INCREMENT = 1;");
        $this->pixie->db->get()->execute("alter table tbl_categories AUTO_INCREMENT = 1;");
    }

    protected function stringContains($haystack, array $needles)
    {
        foreach ($needles as $needle) {
            if (strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    public function generateFilename($fileName, $length = 64)
    {
        return FSHelper::cleanFileName($fileName, $length);
    }

    public function copyFile($from, $to)
    {
        if ($from && $to) {
            if (file_exists($to) && is_file($to)) {
                unlink($to);
            }
            copy($from, $to);
        }
    }
} 