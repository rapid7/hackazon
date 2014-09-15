<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 19.08.2014
 * Time: 13:03
 */


namespace App\EventDispatcher;


use App\Pixie;

/**
 * Dispatches events throughout the system.
 * Inspired by Symfony2 event system.
 * @package App\EventDispatcher
 */
class EventDispatcher
{
    /**
     * @var \App\Pixie
     */
    protected $pixie;

    protected $listeners = [];

    protected $sorted = [];

    public function __construct(Pixie $pixie)
    {
        $this->pixie = $pixie;
    }

    /**
     * @param $eventName
     * @param Event $event
     * @return \App\EventDispatcher\Event
     */
    public function dispatch($eventName, Event $event = null)
    {
        if ($event === null) {
            $event = new Event();
        }

        $event->setDispatcher($this);
        $event->setName($eventName);
        $event->setPixie($this->pixie);

        if (!isset($this->listeners[$eventName])) {
            return $event;
        }

        $this->doDispatch($this->getListeners($eventName), $eventName, $event);

        return $event;
    }

    /**
     * @param $eventName
     * @param $listener
     * @param int $priority
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        if (!is_array($this->listeners[$eventName])) {
            $this->listeners[$eventName] = [];
        }

        if (!is_array($this->listeners[$eventName][$priority])) {
            $this->listeners[$eventName][$priority] = [];
        }

        $this->listeners[$eventName][$priority][] = $listener;
        unset($this->sorted[$eventName]);
    }

    /**
     * @param $eventName
     * @param $listener
     */
    public function removeListener($eventName, $listener)
    {
        if (!isset($this->listeners[$eventName])) {
            return;
        }

        foreach ($this->listeners[$eventName] as $priority => $listeners) {
            if (($key = array_search($listener, $listeners, true)) !== false) {
                unset($this->listeners[$eventName][$priority][$key], $this->sorted[$eventName]);
            }
        }
    }

    /**
     * @param null|string $eventName
     * @return array
     */
    public function getListeners($eventName = null)
    {
        if (null !== $eventName) {
            if (!isset($this->sorted[$eventName])) {
                $this->sortListeners($eventName);
            }

            return $this->sorted[$eventName];
        }

        foreach (array_keys($this->listeners) as $eventName) {
            if (!isset($this->sorted[$eventName])) {
                $this->sortListeners($eventName);
            }
        }

        return $this->sorted;
    }

    /**
     * @param null $eventName
     * @return bool
     */
    public function hasListeners($eventName = null){
        return (bool) count($this->getListeners($eventName));
    }

    /**
     * @param $listeners
     * @param $eventName
     * @param Event $event
     */
    protected function doDispatch($listeners, $eventName, Event $event)
    {
        foreach ($listeners as $listener) {
            call_user_func($listener, $event, $eventName, $this);
            if ($event->isPropagationStopped()) {
                break;
            }
        }
    }

    /**
     * @param $eventName
     */
    protected function sortListeners($eventName)
    {
        $this->sorted[$eventName] = [];

        if (isset($this->listeners[$eventName])) {
            krsort($this->listeners[$eventName]);
            $this->sorted[$eventName] = call_user_func_array('array_merge', $this->listeners[$eventName]);
        }
    }
} 