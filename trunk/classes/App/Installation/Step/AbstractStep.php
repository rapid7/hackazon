<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 08.09.2014
 * Time: 10:59
 */


namespace App\Installation\Step;
use App\Core\View;
use App\Exception\NotFoundException;
use App\Installation\Result;
use App\Pixie;

/**
 * Common installation step features.
 * @package App\Installation\Step
 */
class AbstractStep
{
    /**
     * @var AbstractStep|null
     */
    protected $prev;

    /**
     * @var AbstractStep|null
     */
    protected $next;

    /**
     * @var bool
     */
    protected $isValid = false;

    /**
     * @var bool Indicates whether step is allowed to be executed
     */
    protected $isStarted = false;

    /**
     * @var array Collection of request errors.
     */
    protected $errors = [];

    /**
     * @var string Template to show
     */
    protected $template = '';

    /**
     * @var string Url part that identifies the step
     */
    protected $name = '';

    /**
     * @var mixed|string Description string used in layout
     */
    protected $title = '';

    /**
     * @var Pixie
     */
    protected $pixie;

    /**
     * @var View
     */
    protected $view;

    /**
     * @var bool
     */
    protected $completed = false;

    /**
     * Constructs step
     */
    public function __construct()
    {
        $className = get_class($this);
        $simpleClassName = preg_replace('|^.*\\\\|', '', $className);
        $nameWithoutStep = preg_replace('/Step$/i', '', $simpleClassName);

        if (!$this->name) {
            $this->name = preg_replace('/((?<=[\w\d])[A-Z][a-z0-9]+)/', '_$1', $nameWithoutStep);
            $this->name = strtolower($this->name);
        }

        if (!$this->title) {
            $this->title = preg_replace('/((?<=[\w\d])[A-Z][a-z0-9]+)/', ' $1', $nameWithoutStep);
        }
    }

    public function __sleep()
    {
        return array_merge(['prev', 'next', 'isValid', 'isStarted', 'errors', 'template', 'name', 'title', 'completed'],
            $this->persistFields()
        );
    }

    /**
     * @return AbstractStep|null
     */
    public function getNextStep()
    {
        return $this->next;
    }

    /**
     * @param AbstractStep|null $next
     * @return \App\Installation\Step\AbstractStep|null Added step
     */
    public function chainNextStep(AbstractStep $next)
    {
        $this->next = $next;
        $next->setPixie($this->pixie);
        $next->setView($this->view);
        $next->setPrevStep($this);

        return $next;
    }

    /**
     * @return AbstractStep|null
     */
    public function getPrevStep()
    {
        return $this->prev;
    }

    /**
     * @param AbstractStep|null $prev
     */
    public function setPrevStep($prev)
    {
        $this->prev = $prev;
    }

    /**
     * @param string $method
     * @param array $data
     * @param null $task Optional task if the step requires additional actions.
     * @return array|void|Result
     * @throws \App\Exception\NotFoundException
     */
    public function execute($method = 'GET', array $data = [], $task = null)
    {
        $this->errors = [];
        $this->isValid = true;
        $this->completed = false;

        $result = new Result();
        $result->setStep($this);

        if ($task) {
            $taskName = 'task_' . $task;
            if (method_exists($this, $taskName)) {
                $taskResult = $this->$taskName($method, $data);
                $result->setViewData(array_merge($this->getViewData(), $taskResult));

            } else {
                throw new NotFoundException;
            }

        } else if ($method == 'POST') {
            $this->completed = false;

            if ($this->processRequest($data)) {
                $this->completed = true;
                // If this is the last step - all steps are completed.
                if (!$this->getNextStep()) {
                    $result->setCompleted(true);
                }

            } else {
                $result->setViewData($this->getViewData());
            }

        } else {
            $result->setViewData($this->getViewData());
        }

        return $result;
    }

    /**
     * Checks Step Validity
     * @return bool
     */
    public function isValid()
    {
        return $this->isValid;
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        return $this->isStarted;
    }

    /**
     * Mark step as started
     */
    public function start()
    {
        $this->isStarted = true;
    }

    /**
     * @param array $data
     * @return bool
     */
    protected function processRequest(array $data = [])
    {
        return true;
    }

    /**
     * @return array
     */
    public function getViewData() {
        return [
            'errors' => $this->errors,
            'step' => $this
        ];
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !!count($this->errors);
    }

    /**
     * @return array Error array after validation
     */
    public function getErrors()
    {
        return $this->errors;
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

    /**
     * @return View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param View $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    public function propagateSettings(Pixie $pixie, View $view, $init = true)
    {
        $this->setPixie($pixie);
        $this->setView($view);
        if ($init) {
            $this->init();
        }

        if ($nextStep = $this->getNextStep()) {
            $nextStep->propagateSettings($pixie, $view, $init);
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed|string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return boolean
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Initialize step logic. Should be implemented in implemented steps.
     */
    public function init()
    {
    }

    /**
     * @return array Array of step-specific fields to serialize via __sleep()
     */
    protected function persistFields()
    {
        return [];
    }
}