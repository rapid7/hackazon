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
        $this->_valueVariants[2] = [100, 500];
        $this->_valueVariants[3] = [500, 1000];
        $this->_valueVariants[4] = [10000, 20000];
        $this->_valueVariants[5] = [20000, 200000];
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
        return '';
    }

    public function getFieldName() {
        return 'price-filter';
    }

    public function getVariants() {
        return $this->_valueVariants;
    }

    public function getLabel($id) {
        return isset($this->_valueVariants[$id]) ? 'from $'.$this->_valueVariants[$id][0].' to $'.$this->_valueVariants[$id][1] : '--';
    }

}