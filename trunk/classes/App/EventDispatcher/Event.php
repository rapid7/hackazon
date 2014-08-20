<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 19.08.2014
 * Time: 13:06
 */


namespace App\EventDispatcher;
use App\Pixie;

/**
 * Base event.
 * @package App\EventDispatcher
 */
class Event 
{
    /**
     * @var string Event's name
     */
    protected $name;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var Pixie
     */
    protected $pixie;

    protected $propagationStopped = false;

    /**
     * @return bool
     */
    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }

    /**
     * Stops further propagation of the event.
     */
    public function stopPropagation()
    {
        $this->propagationStopped = true;
    }

    /**
     * @param EventDispatcher $dispatcher
     */
    public function setDispatcher(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return EventDispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return Pixie
     */
    public function getPixie()
    {
        return $this->pixie;
    }

    /**
     * @param Pixie $pixie
     */
    public function setPixie($pixie)
    {
        $this->pixie = $pixie;
    }
}