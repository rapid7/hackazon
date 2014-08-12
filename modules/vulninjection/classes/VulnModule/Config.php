<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 07.08.2014
 * Time: 11:46
 */


namespace VulnModule;

use App\Pixie;
use VulnModule\Config\Context;

/**
 * Class Config. Recursive vulnerability config
 * @package PHPixie
 */
class Config implements \ArrayAccess
{
    /**
     * @var Pixie
     */
    protected $pixie;

    /**
     * Root context in config context tree
     * @var Context
     */
    protected $rootContext;

    /**
     * Current context in context tree
     * @var Context
     */
    protected $currentContext;

    /**
     * @var array
     */
    protected $possibleProps = [
        'fields' => 'getFields',
        'vulnerabilities' => 'getVulnerabilities'
    ];

    /**
     * Current depth of the config.
     * @var int
     */
    protected $level = 0;


    /**
     * Constructs config.
     * @param Pixie $pixie
     * @param Context $rootContext
     */
    public function __construct(Pixie $pixie, Context $rootContext = null)
    {
        $this->pixie = $pixie;
        $this->rootContext = $rootContext;
    }

    /**
     * Create config instance from config data.
     * @param array $data Data from config file
     * @return $this
     */
    public function createFromData($data = [])
    {
        if (!is_array($data)) {
            $data = [];
        }
        $context = Context::createFromData('root', $data);
        $this->rootContext = $context;
        $this->currentContext = $this->rootContext;

        return $this;
    }

    /**
     * @return Context
     */
    public function getRootContext()
    {
        return $this->rootContext;
    }

    /**
     * Add controller context to the root context.
     * @param Context $context
     * @throws \Exception
     */
    public function addControllerContext(Context $context)
    {
        if ($this->rootContext === null) {
            throw new \Exception('You can create controller config only after have created root context');
        }
        $this->rootContext->addContext($context);
    }

    /**
     * Set new current context.
     * @param $context
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setCurrentContext($context)
    {
        if (is_string($context)) {

            $searchingContext = $this->currentContext ?: $this->rootContext;
            $contextObj = $searchingContext->findByPath($context);

            if (!$contextObj) {
                throw new \InvalidArgumentException('Given path is not in config context tree.');
            }

            $this->currentContext = $contextObj;

            return $this;

        } else if ($context instanceof Context) {
            if ($this->rootContext->findInTree($context)) {
                $this->currentContext = $context;
                return $this;

            } else {
                throw new \InvalidArgumentException('Given context is not in config context tree.');
            }
        }

        throw new \InvalidArgumentException(
            '$context must be valid path name or instance of \\VulnModule\\Config\\Context');
    }

    /**
     * Finish current context.
     *
     * @return $this
     */
    public function goUp()
    {
        if ($this->currentContext && $this->currentContext !== null) {
            $parent = $this->currentContext->getParent();
            if ($parent) {
                $this->currentContext = $parent;
                $this->level--;
            }
        }

        return $this;
    }

    /**
     * Dive into child context.
     *
     * @param $path
     * @return $this
     */
    public function goDown($path)
    {
        $context = $this->currentContext ?: $this->rootContext;
        $target = $context->findByPath($path);
        if ($target) {
            $this->setCurrentContext($target);
            $this->level++;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        if (!array_key_exists($offset, $this->possibleProps)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        if (!array_key_exists($offset, $this->possibleProps)) {
            return null;
        }
        $method = $this->possibleProps[$offset];
        return $this->currentContext->$method();
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        throw new \LogicException('You can\'t unset this prop.');
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        throw new \LogicException('You can\'t unset this prop.');
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->currentContext->getFields() ?: [];
    }

    /**
     * @return array
     */
    public function getVulnerabilities()
    {
        return $this->currentContext->getVulnerabilities() ?: [];
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function has($path)
    {
        return !!$this->currentContext->findByPath($path);
    }

    /**
     * @return Context
     */
    public function getCurrentContext()
    {
        return $this->currentContext;
    }
}