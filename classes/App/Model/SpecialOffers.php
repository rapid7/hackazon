<?php

namespace App\Model;

class SpecialOffers extends Product {

    public $table = 'tbl_special_offers';
    public $id_field = 'offerID';

    protected $belongs_to=array(
        //Set the name of the relation, this defines the
        //name of the property that you can access this relation with
        'product_offers'=>array(
            //name of the model to link
            'model'=>'Product',
            //key in 'fairies' table
            'key'=>'productID'
        )
    );


    public function getSpecialOffers(){
       $special_offers = array();
       $offers = $this->pixie->orm->get('SpecialOffers')->order_by('sort_order','asc')->find_all();
       foreach ($offers as $offer){
            $special_offers[] = $this->getProductData($offer->product_offers);
        }
       return $special_offers;
    }

}