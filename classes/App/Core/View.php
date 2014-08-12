<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 08.08.2014
 * Time: 15:53
 */


namespace App\Core;
use App\Pixie;

/**
 * Class View
 * @property Pixie $pixie
 * @package App\Core
 */
class View extends \PHPixie\View implements \ArrayAccess
{

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->_data);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        if (isset($this->_data[$offset])) {
            return $this->_data[$offset];
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        if (isset($this->_data[$offset])) {
            unset($this->_data[$offset]);
        }
    }

    public function render()
    {
        extract($this->helper->get_aliases());
        extract($this->_data);
        ob_start();
        include($this->path);
        return ob_get_clean();
    }
}