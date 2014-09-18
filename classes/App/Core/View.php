<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 08.08.2014
 * Time: 15:53
 */


namespace App\Core;
use App\Core\View\Helper;
use App\Pixie;

/**
 * Class View
 * @property Pixie $pixie
 * @package App\Core
 * @property string $common_path
 * @property string $returnUrl
 * @property BaseController $controller
 * @property Helper $helpers
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

    public function getToken($name)
    {
        return $this->pixie->vulnService->getToken($name);
    }

    /**
     * @return Helper
     */
    public function getHelper()
    {
        return $this->helper;
    }
}