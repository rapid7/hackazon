<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 21.01.2015
 * Time: 12:32
 */


namespace App\Core;


class Router extends \PHPixie\Router
{
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Generates URL by given name and parameters.
     *
     * @param string $routeName Route name
     * @param array $params controller, action, and so on
     * @param bool $absolute Whether link is absolute or not
     * @param string $protocol
     * @return string
     */
    public function generateUrl($routeName = 'default', array $params = array(), $absolute = false, $protocol = 'http', $filterParams = true)
    {
        if (!isset($params['action'])) {
            $params['action'] = false;
        }
        $route = $this->get($routeName);

        $diff = array_diff_key($params, array_flip(['namespace', 'controller', 'action']));
        $diff = array_filter($diff, function ($val) {
            return $val === null || $val === false || $val === '' ? null : $val;
        });
        if (!count($diff)) {
            if (!$params['action'] || $params['action'] == $route->defaults['action']) {
                $params['action'] = '';

                if ($params['controller'] == $route->defaults['controller']) {
                    $params['controller'] = '';
                }
            }
        }
        if ($params['controller'] == 'home') {
           // var_dump($diff, $params, $route->defaults);exit;
        }
        $url = $route->url($params, $absolute, $protocol, $filterParams);
        $url = preg_replace('|\/+$|', '', $url);
        return $url ?: '/';
    }

    /**
     * @inheritdoc
     */
    protected function rule($str)
    {
        $str = $str[0];
        $regexp = '[\\w\\d\\-\\.\\s_%]+';
        if(is_array($this->temp_rule)) {
            $regexp = $this->pixie->arr($this->temp_rule[1], str_replace(array('<', '>'), '', $str), $regexp);
        }
        return '(?P'.$str.$regexp.')';
    }
}