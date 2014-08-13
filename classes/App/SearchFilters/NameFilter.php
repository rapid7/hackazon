<?php

namespace App\SearchFilters;

class NameFilter implements BaseFilter {

    protected $_value;
    protected $pixie;

    public function __construct($pixie) {
        $this->pixie = $pixie;
        $this->init();
    }

    private function init() {
    }

    public function getValue() {
        return $this->_value;
    }

    public function setValue($value = '') {
        $this->_value = $value;
    }

    public function getSql(&$model) {
        $model->where('name', 'LIKE', '%'.$this->getValue().'%');
    }

    public function hasValue() {
        return !empty($this->_value);
    }

    public function getFieldName() {
        return 'name-filter';
    }

}