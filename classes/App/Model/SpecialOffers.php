<?php

namespace App\Model;
use App\Helpers\ArraysHelper;
use PHPixie\DB\PDOV\Result;
use PHPixie\ORM;

/**
 * Class SpecialOffers.
 * @property Product product_offers
 * @package App\Model
 */
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

    /**
     * @param int $count
     * @return mixed|Result
     */
    public function getSpecialOffersList($count = 5)
    {
        return $this->pixie->orm->get('SpecialOffers')->order_by('sort_order','asc')->limit($count)->find_all();
    }

    /**
     * Selects {$maxCount} random reviews from DB.
     *
     * @param int $maxCount Maximum count of selected items
     *      (if table contains less items).
     * @return array
     */
    public function getRandomOffers($maxCount)
    {
        /** @var ORM $orm */
        $orm = $this->pixie->orm;
        $offerCount = $orm->get('specialOffers')->count_all();
        $offsets = ArraysHelper::getRandomArray($maxCount, 1, $offerCount);
        $offers = [];
        // Query for every product with given offset
        foreach ($offsets as $offset) {
            $offer = $orm->get('specialOffers')->offset($offset - 1)->find();
            $offers[] = $offer;
        }

        return $offers;
    }
}