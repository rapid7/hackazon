<?php

namespace App\Model;

use PHPixie\ORM;

class Option extends \PHPixie\ORM\Model {

    public $table = 'tbl_product_options';
    public $id_field = 'optionID';

    protected $has_many=array(
        'variants'=>array(
            'model'=>'OptionValue',
            'key'=>'optionID'
        ),
    );
}