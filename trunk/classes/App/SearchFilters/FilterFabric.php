<?php

namespace App\SearchFilters;

class FilterFabric {

    protected $_filters = [];
    protected $_model;
    protected $_pixie;
    protected $_request;

    private $_filterConfig = ['Price' => 'App\SearchFilters\PriceFilter',
        'Quality' => 'App\SearchFilters\QualityFilter',
        'Brand' => 'App\SearchFilters\BrandFilter'];


    public function __construct($pixie, $request, \PHPixie\ORM\Model $model) {
        $this->_request = $request;
        $this->_model = $model;
        $this->_pixie = $pixie;
        $this->init();
    }

    private function init() {
        foreach ($this->_filterConfig as $name => $className) {
            $this->addFilter($name, $className);
        }
    }

    public function getFilter($name) {
        return isset($this->_filters[$name]) ? $this->_filters[$name] : null;
    }

    public function addFilter($filterName, $filterClass, $filterElementName = '') {
        if (!isset($this->_filters[$filterName]) && class_exists($filterClass)) {
            $this->_filters[$filterName] = new $filterClass($this->_pixie);
            $elementName = empty($filterElementName) ? $this->_filters[$filterName]->getFieldName() : $filterElementName;
            $postData = $this->_request->post($elementName);
            $value = !empty($postData) ? $postData : $this->_request->get($elementName);
            $this->_filters[$filterName]->setValue($value);
        }
    }


    public function getResults() {
        foreach ($this->_filters as $name => $filter) {
            if ($filter->hasValue()) {
               $filter->getSql($this->_model);
            }
        }
        return $this->_model->find_all()->as_array();
    }

}