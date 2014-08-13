<?php

namespace App\SearchFilters;

interface BaseFilter {

    public function getValue();

    public function setValue($value);

    public function hasValue();

    public function getSql(&$model);

    public function getFieldName();

}