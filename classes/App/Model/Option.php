<?php

namespace App\Model;

use PHPixie\ORM;

/**
 * Class Option
 * @package App\Model
 * @property string name
 * @property int $sort_order
 * @property OptionValue $variants
 */
class Option extends BaseModel {

    public $table = 'tbl_product_options';
    public $id_field = 'optionID';

    protected $has_many=array(
        'variants'=>array(
            'model'=>'OptionValue',
            'key'=>'optionID'
        ),
    );
}