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

    public function getValuesForOption()
    {
        if (!$this->loaded()) {
            throw new \InvalidArgumentException("Option provided does not exist.");
        }

        $values = $this->variants->find_all()->as_array();
        $result = [];
        /** @var \App\Model\OptionValue[] $values */
        foreach ($values as $val) {
            $result[$val->id()] = $val->name;
        }

        return $result;
    }
}