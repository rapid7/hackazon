<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 07.08.2014
 * Time: 10:49
 */


namespace App\Core;

use App\EventDispatcher\Events;
use App\Events\GetResponseEvent;
use App\Pixie;

/**
 * Class Request
 * @package App\Core
 * @property Pixie $pixie
 * @inheritdoc
 */
class Request extends \PHPixie\Request
{
    /**
     * @inheritdoc
     */
    public function get($key = null, $default = null, $filter_xss = false)
    {
        return parent::get($key, $default, $filter_xss);
    }

    /**
     * @inheritdoc
     */
    public function post($key = null, $default = null, $filter_xss = false)
    {
        return parent::post($key, $default, $filter_xss);
    }

    /**
     * @inheritdoc
     */
    public function param($key = null, $default = null, $filter_xss = false)
    {
        return parent::param($key, $default, $filter_xss);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $event = new GetResponseEvent($this, $this->_cookie);
        $this->pixie->dispatcher->dispatch(Events::KERNEL_PRE_EXECUTE, $event);

        if ($event->getResponse()) {
            return $event->getResponse();
        }
        return parent::execute();
    }
}