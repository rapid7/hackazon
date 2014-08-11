<?php

namespace App\Model;

use PHPixie\ORM;

class OptionValue extends \PHPixie\ORM\Model {

    public $table = 'tbl_products_opt_val_variants';
    public $id_field = 'variantID';

    protected $belongs_to=array(
        'option'=>array(
            'model'=>'Option',
            'key'=>'optionID'
        )
    );
}