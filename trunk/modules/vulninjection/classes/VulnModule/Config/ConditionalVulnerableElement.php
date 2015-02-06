<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 30.11.2014
 * Time: 16:05
 */


namespace VulnModule\Config;


use App\Core\Request;
use VulnModule\VulnerabilitySet;

class ConditionalVulnerableElement extends VulnerableElement
{
    /**
     * @var ConditionSet
     */
    protected $conditions;

    function __construct(VulnerabilitySet $vulnerabilitySet = null, ConditionSet $conditionSet = null)
    {
        parent::__construct($vulnerabilitySet);
        $this->conditions = $conditionSet ?: new ConditionSet();
    }

    /**
     * @return ConditionSet
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param ConditionSet $conditions
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * @param Request $request
     * @return ConditionalVulnerableElement|null
     */
    public function match(Request $request = null)
    {
        // For testing outside request context we do not use conditional subtrees
        if (!$request && $this->conditions->count()) {
            return null;
        }

        $matches = true;
        foreach ($this->conditions->getConditions() as $condition) {
            if (!$condition->match($request)) {
                $matches = false;
                break;
            }
        }

        if ($matches && $this->children->count()) {
            foreach ($this->children as $child) {
                if ($matched = $child->match($request)) {
                    return $matched;
                }
            }
        }

        return $matches ? $this : null;
    }

    public function hasConditions()
    {
        return $this->conditions->count() > 0;
    }

    public function getDefaultElement()
    {
        $default = parent::getDefaultElement();
        return $default === $this ? ($default->hasConditions() ? null : $default) : $default;
    }
}