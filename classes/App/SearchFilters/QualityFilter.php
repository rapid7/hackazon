<?php

namespace App\SearchFilters;

class QualityFilter implements BaseFilter {

    protected $_value = [];
    protected $_valueVariants = [];
    protected $pixie;
    const OPTION_NAME = 'Quality';

    public function __construct($pixie) {
        $this->pixie = $pixie;
        $this->init();
    }

    private function init() {
        $option = $this->pixie->orm->get('Option')->where('name', self::OPTION_NAME)->find();
        if ($option->loaded()) {
            $variants = $option->variants->find_all();
            foreach ($variants as $var) {
                $this->_valueVariants[$var->variantID] = $var->name;
            }
        }
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
        return 'quality-filter';
    }

    public function getVariants() {
        return $this->_valueVariants;
    }
}