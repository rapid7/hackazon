<?php

namespace App\Model;

use App\Core\Request;
use App\Helpers\ArraysHelper;
use App\Pixie;
use PHPixie\ORM;
use VulnModule\VulnerableField;

/**
 * Class Product.
 *
 * @property int productID
 * @property string name
 * @property string description
 * @property float Price
 * @property float customers_rating
 * @property string picture
 * @property int categoryID
 * @property mixed $customer_votes
 * @property Pixie pixie
 * @property SpecialOffers $special_offers
 * @property Review $reviews
 * @property WishList $in_wishlists
 * @property Category $categories
 * @property Category $category
 * @property OptionValue $options
 * @property ProductOptionValue $productOptions
 * @package App\Model
 */
class Product extends BaseModel {

    public $table = 'tbl_products';
    public $id_field = 'productID';
    private $annotation_length = 20;

    protected $has_one=array(

        //Set the name of the relation, this defines the
        //name of the property that you can access this relation with
        'special_offers'=>array(

            //name of the model to link
            'model'=>'SpecialOffers',

            //key in 'fairies' table
            'key'=>'productID'
        )
    );

    protected $has_many = array(
        'reviews'=>array(
            'model'=>'Review',
            'key'=>'productID'
        ),
        'in_wishlists' => array(
            'model' => 'wishList',
            'through' => 'tbl_wish_list_item',
            'foreign_key' => 'wish_list_id',
            'key' => 'product_id'
        ),
        'categories' => array(
            'model' => 'Category',
            'through' => 'tbl_category_product',
            'foreign_key' => 'CategoryID',
            'key' => 'ProductID'
        ),
        'options' => array(
            'model' => 'OptionValue',
            'through' => 'tbl_product_options_values',
            'foreign_key' => 'ID',
            'key' => 'productID'
        ),
        'productOptions' => array(
            'model' => 'ProductOptionValue',
            'key' => 'productID'
        )
    );

    protected $belongs_to = [
        'category' => [
            'model' => 'Category',
            'key' => 'categoryID'
        ]
    ];

    public function getProduct($productID){
        $productData = array();
        /** @var Product $product */
        $product = $this->pixie->orm->get('Product')->where('productID',$productID)->find();
        if($product->loaded()){
            $productData = array(
                'productID' => $productID,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->Price,
                'customers_votes'  => $product->customer_votes,
                'customers_rating'  => $product->customers_rating,
                'picture' => $product->picture,
                'reviews' => $product->getReviews()
            );
        }
        return $productData;
    }

    public  function getPageTitle($productID){
        /** @var Product $product */
        $product = $this->pixie->orm->get('Product')->where('productID',$productID)->find();
        if ($product->loaded()) {
            return $product->name;

        } else {
            return null;
        }
    }

    /**
     * Returns random products.
     *
     * @todo This implementation is bad because it loads all of the products
     *      (imagine 10000 products in the DB). Below is correct implementation.
     *      This one can be removed if it isn't used somewhere.
     * @param $count_rnd
     * @return array
     */
    public function getRndProduct($count_rnd){
        $products = $this->pixie->orm->get('Product')->find_all();
        $rnd_array = $this->getRndArray($count_rnd, $this->pixie->orm->get('Product')->count_all());
        $i=1;
        $rnd_products = array();
        foreach ($products as $product){
            if(in_array($i,$rnd_array))
                $rnd_products[] = $this->getProductData($product);

            $i++;
        }
        return $rnd_products;
    }

    /**
     * Returns array of random values from 1 to given max value.
     *
     * @param $count_rnd
     * @param $max_array_value
     * @return array
     */
    private function getRndArray($count_rnd, $max_array_value){
        return ArraysHelper::getRandomArray($count_rnd, 1, $max_array_value);
    }

    /**
     * Selects {$maxCount} random products from DB.
     *
     * @param int $maxCount Maximum count of selected items
     *      (if table contains less items).
     * @return array
     */
    public function getRandomProducts($maxCount)
    {
        /** @var ORM $orm */
        $orm = $this->pixie->orm;
        $productCount = $orm->get('product')->count_all();
        $offsets = $this->getRndArray($maxCount, $productCount);

        $products = [];
        // Query for every product with given offset
        foreach ($offsets as $offset) {
            $product = $orm->get('product')->offset($offset - 1)->find();
            $products[] = $product;
        }

        return $products;
    }

    /**
     * Returns either brief description of product if it exists, or trims
     *      full description.
     * @param null $length
     * @return mixed|string
     */
    public function getAnnotation($length = null)
    {
        $annoLength = $this->annotation_length;
        if (!is_null($length)) {
            $this->annotation_length = $length;
        }
        $annotation = $this->_getBrief($this->description);
        $this->annotation_length = $annoLength;
        return $annotation;
    }

    /**
     * @param $length
     * @throws \InvalidArgumentException
     */
    public function setAnnotationLength($length)
    {
        if ($length <= 0 || !is_numeric($length)) {
            throw new \InvalidArgumentException("Length must be number > 0.");
        }
        $this->annotation_length = $length;
    }

    /**
     * Returns product data as an array.
     * @param $product
     * @return array
     */
    public function getProductData($product){
        return array(
            'productID' => $product->productID,
            'name' => $product->name,
            'price' => $product->Price,
            'annotation' => ((empty($product->brief_description))? $this->_getBrief($product->description):$product->brief_description),
            'thumbnail' => $product->thumbnail,
            'customers_votes' => $product->customer_votes,
            'customers_rating'  => $product->customers_rating,
            'picture' => $product->picture
        );
    }

    public function checkProductInCookie($productId)
    {
        if ($productId instanceof VulnerableField) {
            $productId = $productId->raw();
        }
        $productIds = $this->pixie->cookie->get('visited_products');
        if (!$productIds) {
            $productIds = ',';
        }
        if (strpos($productIds, ",$productId,") === false) {
            $this->pixie->cookie->set('visited_products', $productIds . $productId . ',', 3600 * 24 * 365, '/');
        }
    }

    /**
     * Fetches no more than $count visited products based on cookies.
     * @param string $productIds
     * @param int $count
     * @return array
     */
    public function getVisitedProducts($productIds, $count = 4)
    {
        $rawIds = $productIds instanceof VulnerableField ? $productIds->raw() : $productIds;
        $ids = preg_split('/,/', $rawIds, -1, PREG_SPLIT_NO_EMPTY);

        $ids = array_filter($ids, function ($val) { return trim($val); });
        $idsCount = count($ids);

        // Return empty array if there is no ids in cookies.
        if (!$idsCount) {
            return [];
        }

        // Select no more then $count product ids
        $slicedIdsKeys = array_rand($ids, $count > $idsCount ? $idsCount : ($count < 1 ? 1 : $count));
        if (!is_array($slicedIdsKeys)) {
            $slicedIdsKeys = [$slicedIdsKeys];
        }

        shuffle($slicedIdsKeys);
        $idsToSelect = array();
        foreach ($slicedIdsKeys as $key) {
            $idsToSelect[$key] = $ids[$key];
        }

        // Select needed products by their ids
        /** @var Product $product */
        $product = $this->pixie->orm->get('product');
        if ($productIds instanceof VulnerableField) {
            $isSQLVuln = $productIds->isVulnerableTo('SQL');
            if (!$isSQLVuln) {
                foreach ($idsToSelect as $k => $id) {
                    $idsToSelect[$k] = (int) $id;
                }
            }
        }

        $idsExpr = '(' . implode(',', $idsToSelect) . ')';

        /** @var Product $query */
        $query = $product->where('productID', 'IN', $this->pixie->db->expr($idsExpr));

        $query->conn->startBlindness($productIds);
        $result = $query->find_all()->as_array();
        $query->conn->stopBlindness();

        return $result;
    }

    /**
     * Returns shortened excerpt of given string.
     * @param $content
     * @return string
     */
    private function _getBrief($content){
        $annotation = strip_tags($content);
        $annotation = substr($annotation, 0, $this->annotation_length);
        $annotation = rtrim($annotation, "!,.-");
        $annotation = substr($annotation, 0, strrpos($annotation, ' '));
        return $annotation;
    }
    public function getReviews() {
        return $this->reviews->where('moder', '=', Review::APPROVED)->find_all();
    }

    public function isInUserWishList(User $user = null)
    {
        if ($user === null || !$user->loaded() || !$this->loaded()) {
            return false;
        }

        $num = $this->pixie->db->query('count')->table('tbl_products', 'p')
            ->join(array('tbl_wish_list_item', 'wli'), array('wli.product_id', 'p.productID'))
            ->join(array('tbl_wish_list', 'wl'), array('wl.id', 'wli.wish_list_id'))
            ->where('wl.user_id', '=', $user->id())
            ->where('p.productID', '=', $this->id())
            ->execute();

        return $num > 0;
    }

}