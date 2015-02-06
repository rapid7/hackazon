<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 24.12.2014
 * Time: 12:51
 */


namespace VulnModule\Config;


use App\Pixie;

class Config
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
        $this->currentContext = $rootContext;
    }

    /**
     * @return Context
     */
    public function getRootContext()
    {
        return $this->rootContext;
    }

    /**
     * @return Context
     */
    public function getCurrentContext()
    {
        return $this->currentContext;
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
     * @param $childName
     * @param bool $createIfNotExists
     * @return $this
     * @throws \Exception
     */
    public function goDown($childName, $createIfNotExists = true)
    {
        $context = $this->currentContext ?: $this->rootContext;
        $target = $context->getChildByName($childName);

        if ($target) {
            $this->currentContext = $target;
            $this->level++;

        } else {
            if ($createIfNotExists) {
                $target = new Context($childName);
                $this->currentContext->addChild($target);
                $target->setRequest($this->currentContext->getRequest());
                $this->currentContext = $target;

            } else {
                throw new \RuntimeException("No such context: \"$childName\"");
            }
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param string|Context $child
     * @return bool
     */
    public function has($child)
    {
        if ($child instanceof Context) {
            return $this->currentContext->hasChild($child);

        } else if (is_string($child)) {
            return $this->currentContext->hasChildByName($child);

        } else {
            return false;
        }
    }

    /**
     * @param $name
     * @return null|\VulnModule\Vulnerability
     */
    public function getVulnerability($name)
    {
        return $this->getCurrentContext()->getVulnerability($name);
    }

    /**
     * @return array|\VulnModule\DataType\ArrayObject
     */
    public function getContextVulnerabilities()
    {
        return $this->getCurrentContext()->getMatchedVulnerabilityElement()->getComputedVulnerabilities();
    }
}