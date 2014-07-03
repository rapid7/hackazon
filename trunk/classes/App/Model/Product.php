<?php

namespace App\Model;

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

    public function getProductsCategory($categoryID){
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
                'picture' => $product->picture
            );
        }
        return $productData;
    }

    public  function getPageTitle($productID){
        $product = $this->pixie->orm->get('Product')->where('productID',$productID)->find();
        if($product->loaded())
            return $product->name;
    }

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

    private function getRndArray($count_rnd, $max_array_value){
        $rnd_array = array();
        for ($i=0;$i<$count_rnd;$i++)
            $rnd_array[]= rand(1, $max_array_value);
        return $rnd_array;
    }

    protected function getProductData($product){
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

    private function _getBrief($content){
        $annotation = strip_tags($content);
        $annotation = substr($annotation, 0, $this->annotation_length);
        $annotation = rtrim($annotation, "!,.-");
        $annotation = substr($annotation, 0, strrpos($annotation, ' '));
        return $annotation;
    }

}