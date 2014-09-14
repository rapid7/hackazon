<?php

namespace App\SearchFilters;

class PriceFilter implements BaseFilter {

    protected $_value;
    protected $_valueVariants = [];
    public $pixie;

    public function __construct($pixie) {
        $this->pixie = $pixie;
        $this->init();
    }

    private function init() {
        $this->_valueVariants[1] = [0, 100];
        $this->_valueVariants[2] = [100, 200];
        $this->_valueVariants[3] = [200, 300];
        $this->_valueVariants[4] = [300, 500];
        $this->_valueVariants[5] = [500, 1000];
    }

    public function getValue() {
        return $this->_value;
    }

    public function setValue($value) {
        $this->_value = $value;
    }

    public function hasValue() {
        return ($this->_value && isset($this->_valueVariants[$this->_value]));
    }

    public function getSql(&$model) {
        $values = $this->_valueVariants[$this->getValue()];
        $model->where('Price', '>=', $values[0])->where('Price', '<=', $values[1]);
    }

    public function getFieldName() {
        return 'price-filter';
    }

    public function getVariants() {
        return $this->_valueVariants;
    }

    public function getLabel($id) {
		return isset($this->_valueVariants[$id]) ? '$'.$this->_valueVariants[$id][0].' &ndash; $'.$this->_valueVariants[$id][1] : '--';
    }

}