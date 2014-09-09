<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 08.09.2014
 * Time: 10:49
 */


namespace App\Installation;
use App\Core\Request;
use App\Core\View;
use App\Helpers\ArraysHelper;
use App\Installation\Step\AbstractStep;
use App\Installation\Step\ConfirmationStep;
use App\Installation\Step\DBSettingsStep;
use App\Installation\Step\EmailSettingsStep;
use PHPixie\Pixie;

/**
 * Performs configuration and DB installation
 * @package App\Installation
 */
class Installer 
{
    const SESSION_KEY = '_installer';
    /**
     * @var bool
     */
    protected $sessionStarted = false;

    /**
     * @var AbstractStep
     */
    protected $firstStep;

    /**
     * @var AbstractStep
     */
    protected $lastStep;

    /**
     * @var View
     */
    protected $view;

    /**
     * @var \App\Pixie
     */
    protected $pixie;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var bool
     */
    protected $initialized = false;

    /**
     * @var array
     */
    protected $stepsData = [];

    public function __construct(Pixie $pixie)
    {
        $this->pixie = $pixie;
    }

    /**
     * @param Request $request
     * @return \App\Installation\Result|array|void
     * @throws \LogicException
     */
    public function runWizard(Request $request)
    {
        if (!$this->initialized) {
            throw new \LogicException('Installator must be initialized before running');
        }

        $this->request = $request;
        $this->checkSessionStarted();

        $step = $request->param('id');
        $result = new Result();

        if (!$step) {
            $step = $this->firstStep->getName();
        }

        // Traverse all steps until current
        $stepObj = $this->firstStep;
        $lastStartedStep = $stepObj;

        while ($stepObj) {
            // Forbid executing not started steps.
            if (!$stepObj->isStarted()) {
                break;
            }

            $lastStartedStep = $stepObj;

            // Execute current step and stop.
            if ($step == $stepObj->getName()) {
                $result = $stepObj->execute(strtoupper($this->request->method), $this->request->getRequestData());

                if ($stepObj->getCompleted()) {
                    if ($nextStep = $stepObj->getNextStep()) {
                        $nextStep->start();

                    } else {
                        $result->setCompleted(true);
                    }
                }
                break;
            }

            // If invalid step is before current one, stop propagation, and ask user to fix it.
            if (!$stepObj->isValid()) {
                $result->setStep($stepObj);
                $result->redirectToStep();
                break;
            }

            $stepObj = $stepObj->getNextStep();
        }

        $this->stepsData['steps'][$stepObj->getName()]['current'] = true;

        $this->stepsData = ArraysHelper::arrayMergeRecursiveDistinct($this->stepsData, $stepObj->getViewData());
        $result->setViewData($this->stepsData);
        $result->setLastStartedStep($lastStartedStep);
        return $result;
    }

    /**
     * If chain of steps is already in session,
     */
    protected function buildStepChain()
    {
        $this->checkSessionStarted();

        if ($_SESSION[self::SESSION_KEY]['steps'] instanceof AbstractStep) {
            $this->firstStep = $_SESSION[self::SESSION_KEY]['steps'];
            $this->firstStep->propagateSettings($this->pixie, $this->view, false);

        } else {
            $this
                ->addStep(new DBSettingsStep())
                ->addStep(new EmailSettingsStep())
                ->addStep(new ConfirmationStep());

            $this->firstStep->start();
            $this->firstStep->propagateSettings($this->pixie, $this->view);

            $_SESSION[self::SESSION_KEY]['steps'] = $this->firstStep;
        }
    }

    /**
     * @param AbstractStep $step
     * @return Installer
     */
    protected function addStep(AbstractStep $step)
    {
        if (!$this->firstStep) {
            $this->firstStep = $step;
            $this->lastStep = $step;

        } else {
            $this->lastStep->chainNextStep($step);
            $this->lastStep = $step;
        }
        return $this;
    }

    /**
     * @param View $view
     * @return $this
     */
    public function init(View $view)
    {
        if ($this->initialized) {
            return $this;
        }

        $this->view = $view;
        $this->buildStepChain();
        $this->collectViewData();

        $this->initialized = true;

        return $this;
    }

    protected function collectViewData()
    {
        $stepObj = $this->firstStep;
        $this->stepsData = [];

        while ($stepObj) {
            $this->stepsData['steps'][$stepObj->getName()] = [
                'title' => $stepObj->getTitle(),
                'current' => false,
                'started' => $stepObj->isStarted(),
                'valid' => $stepObj->isValid()
            ];
            $stepObj = $stepObj->getNextStep();
        }
    }

    /**
     * Ensure session is enabled.
     */
    public function checkSessionStarted()
    {
        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            if (PHP_SESSION_NONE === session_status()) {
                session_start();
            }
        } elseif (!session_id()) {
            session_start();
        }

        $this->sessionStarted = true;
    }

    public function finish()
    {
        unset($_SESSION[self::SESSION_KEY]);
    }
}