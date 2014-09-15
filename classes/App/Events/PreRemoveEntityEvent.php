<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 15.09.2014
 * Time: 17:09
 */


namespace App\Events;


use App\EventDispatcher\Event;
use App\Model\BaseModel;

class PreRemoveEntityEvent extends Event
{
    /** @var BaseModel */
    protected $entity;

    /**
     * @var bool
     */
    protected $canRemove = true;

    /**
     * @var string
     */
    protected $reason;

    function __construct(BaseModel $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return boolean
     */
    public function getCanRemove()
    {
        return $this->canRemove;
    }

    /**
     * @param boolean $canRemove
     */
    public function setCanRemove($canRemove)
    {
        $this->canRemove = (boolean) $canRemove;
        if (!$this->canRemove) {
            $this->propagationStopped = true;
        }
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param string $reason
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    /**
     * @return BaseModel
     */
    public function getEntity()
    {
        return $this->entity;
    }
} 