<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 22.01.2015
 * Time: 18:02
 */


namespace App\Core;


class Route extends \PHPixie\Route
{
    public function url($params = array(), $absolute = false, $protocol = 'http', $filterParams = true)
    {
        if (is_callable($this->rule))
            throw new \Exception("The rule for '{$this->name}' route is a function and cannot be reversed");

        $url = $this->basepath;
        if (substr($url, -1) === '/')
            $url = substr($url, 0, -1);
        $url.= is_array($this->rule) ? $this->rule[0] : $this->rule;

        $replace = array();
        $params = array_merge($this->defaults, $params);
        foreach ($params as $key => $value)
            $replace["<{$key}>"] = $value;
        $url = str_replace(array_keys($replace), array_values($replace), $url);

        $count = 1;
        $chars = '[^\(\)]*?';
        while ($count > 0)
            $url = preg_replace("#\\({$chars}<{$chars}>{$chars}\\)#", '', $url, -1, $count);

        $url = str_replace(array('(', ')'), '', $url);

        if ($absolute)
            $url = $protocol.'://'.$_SERVER['HTTP_HOST'].$url;

        return $url;
    }
}