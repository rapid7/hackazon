<?php

namespace App\Model;

use PHPixie\ORM;

/**
 * Class OptionValue
 * @package App\Model
 * @property int $variantID
 * @property int $optionID
 * @property string $name
 * @property int $sort_order
 * @property Option $parentOption
 */
class OptionValue extends BaseModel {

    public $table = 'tbl_products_opt_val_variants';
    public $id_field = 'variantID';

    protected $belongs_to=array(
        'parentOption'=>array(
            'model'=>'Option',
            'key'=>'optionID'
        )
    );
}