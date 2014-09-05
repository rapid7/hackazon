<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 05.09.2014
 * Time: 17:10
 */


namespace App\Core;


use App\Helpers\ArraysHelper;

class Config extends \PHPixie\Config
{
    protected $inheritedGroups = [
        'parameters'
    ];

    public function load_inherited_group($name)
    {
        $sampleName = $name . '.sample';

        $file = $this->pixie->find_file('config', $sampleName);
        $this->load_group($sampleName, $file);

        $file = $this->pixie->find_file('config', $name);
        $this->load_group($name, $file);

        $this->groups[$name] = ArraysHelper::arrayMergeRecursiveDistinct($this->groups[$sampleName], $this->groups[$name]);
    }

    public function get_group($name)
    {
        if (isset($this->groups[$name])) {
            return $this->groups[$name]['options'];
        }
        if (in_array($name, $this->inheritedGroups)) {
            $this->load_inherited_group($name);
        }
        return parent::get_group($name);
    }
} 