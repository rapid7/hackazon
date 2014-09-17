<?php

namespace App\Model;

use PHPixie\ORM;

/**
 * Class OptionValue
 * @package App\Model
 * @property int $variantID
 * @property string $name
 * @property int $sort_order
 * @property Option $option
 */
class OptionValue extends BaseModel {

    public $table = 'tbl_products_opt_val_variants';
    public $id_field = 'variantID';

    protected $belongs_to=array(
        'option'=>array(
            'model'=>'Option',
            'key'=>'optionID'
        )
    );
}