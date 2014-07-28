<?php

namespace App\Model;

use PHPixie\ORM;

/**
 * Class Product.
 *
 * @property int productID
 * @property string name
 * @property string description
 * @property float Price
 * @property float customers_rating
 * @property string picture
 * @package App\Model
 */
class Product extends \PHPixie\ORM\Model {

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
        )
    );

    public function getProductsCategory($categoryID) {
        $items = array();
        $products = $this->pixie->orm->get('Product')->where('categoryID',$categoryID)->order_by('name','asc')->find_all();
        if($products){
            $config = $this->pixie->config->get('product');
            $this->annotation_length = $config['annotation_length'];
            foreach ($products as $product)
                $items[] = $this->getProductData($product);
        }
        return $items;
    }


    public function getProduct($productID){
        $productData = array();
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
        $product = $this->pixie->orm->get('Product')->where('productID',$productID)->find();
        if($product->loaded())
            return $product->name;
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
        $rnd_array = array();
        srand();
        // Limit the selected count to the max boundary.
        if ($count_rnd > $max_array_value) {
            $count_rnd = $max_array_value;
        }

        for ($i=0;$i<$count_rnd;) {
            //Only append values which are not in the array.
            $value = rand(1, $max_array_value);
            if (in_array($value, $rnd_array)) {
                continue;
            }
            $rnd_array[] = $value;
            $i++;
        }
        return $rnd_array;
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
     * @return mixed|string
     */
    public function getAnnotation()
    {
        return $this->_getBrief($this->description);
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

}