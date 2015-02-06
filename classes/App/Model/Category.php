<?php

namespace App\Model;
use PHPixie\ORM\Extension\Nested;
use VulnModule\VulnerableField;

/**
 * Class Category
 * @property int categoryID
 * @property string name
 * @property string description
 * @property int parent
 * @property int products_count
 * @property int products_count_admin
 * @property string picture
 * @property string about
 * @property string meta_title
 * @property string meta_keywords
 * @property string meta_desc
 * @property string hurl
 * @property string canonical
 * @property string h1
 * @property int enabled
 * @property int hidden
 * @property Category parentCategory
 * @property Category children
 * @property Product|Product[] $products
 * @property Nested $nested
 * @package App\Model
 */
class Category extends BaseModel {

    public $table = 'tbl_categories';
    public $id_field = 'categoryID';

    public $childs = [];

    protected $belongs_to = array(
        'parentCategory' => array(
            'model' => 'category',
            'key' => 'parent'
        )
    );

    protected $has_many = array(
        'products' => array(
            'model' => 'product',
            'through' => 'tbl_category_product',
            'key' => 'CategoryID',
            'foreign_key' => 'ProductID'
        )
    );

    protected $extensions = array(
        'nested'=>'\PHPixie\ORM\Extension\Nested'
    );

    public  function getPageTitle($categoryID){
        if (!($categoryID instanceof VulnerableField && $categoryID->getFilteredValue()) && !$categoryID) {
            return '';
        }
        $category = $this->loadCategory($categoryID);
        if($category)
            return $category->name;
        return '';
    }

    public function loadCategory($categoryID){
        $category = $this->pixie->orm->get('Category')->where('categoryID', $categoryID)->find();
        if($category->loaded())
            return $category;
        return null;
    }

    public function getRootCategories(){
        return $this->pixie->db->query('select')
                            ->table('tbl_categories')
                            ->where('depth', 1)
                            ->order_by('lpos', 'asc')
                            ->execute();    
    
    }

    public function parents() {
        if (!$this->loaded())
            throw new \Exception("The model is not loaded");
        return $this->pixie->orm->get($this->model_name)
            ->where('lpos', '<', $this->lpos)->where('rpos', '>', $this->rpos)->where('depth', '>', 0)
            ->order_by('lpos', 'asc')->find_all();
    }

    public function getChildrenIDs() {
        if (!$this->loaded())
            throw new \Exception("The model is not loaded");
        $result = [];
        $ids = $this->pixie->db->query('select')->table($this->table)
            ->fields($this->id_field)->where('lpos', '>', $this->lpos)
            ->where('rpos', '<', $this->rpos)->where('depth', '>', 0)->execute()->as_array();
        if (count($ids)) {
            array_walk($ids, function($item) use(&$result) {
                $result[] = $item->categoryID;
            });
        }
        return $result;
    }


    public function getCategoriesSidebar(){
        $categories = $this->pixie->orm->get('Category')->where('depth', 'IN', $this->pixie->db->expr('(0, 1, 2)'))->order_by('lpos', 'asc')->order_by('name','asc')->find_all()->as_array();
        return $this->generateTree($categories);
    }

    protected function generateTree(array &$categories)
    {
        $results = [];
        if (count($categories) > 0) {
            /* Extract root category */
            $root = array_shift($categories);
            $generateItems = function ($level, $lft, $rgt) use (&$categories, &$generateItems) {
                $result = [];
                //Main Level
                foreach ($categories as $value) {
                    if ($value->depth == $level && $value->lpos >= $lft && $value->rpos <= $rgt) {
                        $result[] = $value;
                    }
                }
                foreach ($result as &$value) {
                    $value->childs = $generateItems($level + 1, $value->lpos, $value->rpos);
                }
                return $result;
            };
            /* Get Lft Rgt for Root */
            $results = $generateItems(1, (int)$root->lpos, (int)$root->rpos);
        }
        return $results;
    }
}