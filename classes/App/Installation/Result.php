<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 08.09.2014
 * Time: 15:07
 */


namespace App\Installation;


use App\Installation\Step\AbstractStep;

class Result
{
    /**
     * @var AbstractStep
     */
    protected $step;

    /**
     * @var array
     */
    protected $viewData = [];

    /**
     * @var bool
     */
    protected $completed = false;

    protected $needRedirect = false;

    /**
     * @var AbstractStep
     */
    protected $lastStartedStep;

    /**
     * @return boolean
     */
    public function isCompleted()
    {
        return $this->completed;
    }

    /**
     * @param boolean $completed
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;
    }

    /**
     * @return AbstractStep
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * @param AbstractStep $step
     */
    public function setStep($step)
    {
        $this->step = $step;
    }

    /**
     * @return array
     */
    public function getViewData()
    {
        return $this->viewData;
    }

    /**
     * @param array $viewData
     */
    public function setViewData($viewData)
    {
        $this->viewData = $viewData;
    }

    public function redirectToStep()
    {
        $this->needRedirect = true;
    }

    /**
     * @return boolean
     */
    public function needRedirect()
    {
        return $this->needRedirect;
    }

    public function setLastStartedStep(AbstractStep $step)
    {
        $this->lastStartedStep = $step;
    }

    /**
     * @return AbstractStep
     */
    public function getLastStartedStep()
    {
        return $this->lastStartedStep;
    }
} 