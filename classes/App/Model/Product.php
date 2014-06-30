<?php

namespace App\Model;

class Product extends \PHPixie\ORM\Model {

    public $table = 'tbl_products';
    public $id_field = 'productID';
    private $annotation_length;



    public function getProductsCategory($categoryID){
        $items = array();
        $products = $this->pixie->orm->get('Product')->where('categoryID',$categoryID)->order_by('name','asc')->find_all();
        if($products){
            $config = $this->pixie->config->get('product');
            $this->annotation_length = $config['annotation_length'];
            foreach ($products as $product){
                $items[] = array(
                    'productID' => $product->productID,
                    'name' => $product->name,
                    'price' => $product->Price,
                    'annotation' => ((empty($product->brief_description))? $this->_getBrief($product->description):$product->brief_description),
                    'thumbnail' => $product->thumbnail,
                    'customers_votes' => $product->customer_votes,
                );
            }

        }
        return $items;
    }

    private function _getBrief($content){
        $annotation = strip_tags($content);
        $annotation = substr($annotation, 0, $this->annotation_length);
        $annotation = rtrim($annotation, "!,.-");
        $annotation = substr($annotation, 0, strrpos($annotation, ' '));
        return $annotation;
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

}