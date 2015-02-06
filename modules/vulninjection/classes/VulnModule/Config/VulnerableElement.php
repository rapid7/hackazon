<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 28.11.2014
 * Time: 18:49
 */


namespace VulnModule\Config;


use App\Core\Request;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use VulnModule\DataType\ArrayObject;
use VulnModule\Vulnerability;
use VulnModule\VulnerabilityFactory;
use VulnModule\VulnerabilitySet;

/**
 * Vulnerable Element is an element, which contains no or several vulnerabilities.
 * Also, it inherits missing vulnerabilities from its parents, and caches them.
 * When the structure of the tree changes, the cache of element and its children is cleaned.
 * It's the base of all elements in vulnerability config (contexts, fields, conditional elements).
 *
 * @package VulnModule\Vulnerability
 */
class VulnerableElement
{
    const COMPUTE_ONLY_ROOT = 0x1;
    const COMPUTE_GATHER_CHAIN = 0x2;
    const COMPUTE_CLEAR_CHAIN = 0x4;

    /**
     * @var VulnerabilitySet
     */
    protected $vulnerabilitySet;

    /**
     * @var VulnerableElement[]|ArrayObject<VulnerableElement>|ConditionalVulnerableElement[]|ArrayObject<ConditionalVulnerableElement>
     */
    protected $children;

    /**
     * @var VulnerableElement
     */
    protected $parent;

    /**
     * @var VulnerabilitySet
     */
    protected $cachedVulnerabilities;

    /**
     * @var VulnerabilityHost
     */
    protected $host;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $targets = [
        Vulnerability::TARGET_CONTEXT,
        Vulnerability::TARGET_FIELD
    ];

    /**
     * @param VulnerabilitySet $vulnerabilitySet
     */
    function __construct(VulnerabilitySet $vulnerabilitySet = null)
    {
        $this->vulnerabilitySet = new VulnerabilitySet();
        $this->setVulnerabilitySet($vulnerabilitySet);
        $this->children = new ArrayObject();
        $this->cachedVulnerabilities = new VulnerabilitySet();
        $this->dependencyChain = [];
    }

    /**
     * @param VulnerabilitySet $vulnerabilitySet
     * @return VulnerableElement
     */
    public static function create(VulnerabilitySet $vulnerabilitySet = null)
    {
        return new VulnerableElement($vulnerabilitySet);
    }

    /**
     * @return VulnerabilitySet
     */
    public function getVulnerabilitySet()
    {
        return $this->vulnerabilitySet;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasOwnVulnerability($name)
    {
        return $this->vulnerabilitySet->hasOwnVulnerability($name);
    }

    public function addChild(VulnerableElement $child)
    {
        if (!$child || $this->children->contains($child)) {
            return;
        }

        if ($child->getParent()) {
            $child->getParent()->removeChild($child);
        }
        $this->children->append($child);
        $child->setParent($this);
        $this->clearCache();
    }

    public function removeChild(VulnerableElement $child)
    {
        $this->children->removeElement($child);
        $child->setParent(null);
        return $child;
    }

    public function hasChild(VulnerableElement $child)
    {
        return $this->children->contains($child);
    }

    /**
     * @return VulnerableElement
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param VulnerableElement $parent
     */
    public function setParent(VulnerableElement $parent)
    {
        if ($this->parent === $parent) {
            return;
        }

        if ($this->parent && $this->parent->hasChild($this)) {
            $this->parent->removeChild($this);
        }

        $this->parent = $parent;
        if (!$this->host) {
            $this->setTargets($parent ? $parent->getTargets() : null);
        }
    }

    /**
     * @return VulnerableElement[]|ArrayObject
     */
    public function getChildrenArray()
    {
        return $this->children->getArrayCopy();
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return $this->children->count() > 0;
    }

    /**
     * @return VulnerableElement[]|\ArrayIterator<VulnerableElement>
     */
    public function getChildrenIterator()
    {
        return $this->children->getIterator();
    }

    /**
     * @param Vulnerability[]|ArrayObject<Vulnerability>|VulnerabilitySet $vulnerabilities
     */
    public function setVulnerabilitySet($vulnerabilitySet)
    {
        if (!$vulnerabilitySet) {
            $this->vulnerabilitySet->clear();
            return;
        }

        if ($vulnerabilitySet instanceof VulnerabilitySet) {
            $vulns = $vulnerabilitySet->getVulnerabilities();
        } else {
            $vulns = is_array($vulnerabilitySet) || $vulnerabilitySet instanceof ArrayObject ? $vulnerabilitySet : [];
        }

        /** @var Vulnerability $vuln */
        foreach ($vulns as $key => $vuln) {
            if (!$vuln->isTargetedAt($this->getTargets())) {
                unset($vulns[$key]);
            }
        }

        if (!$this->vulnerabilitySet) {
            $this->vulnerabilitySet = new VulnerabilitySet();
        }
        $this->vulnerabilitySet->setVulnerabilities($vulns);
    }

    /**
     * @param null|string $vulnName
     */
    public function clearCache($vulnName = null)
    {
        if ($vulnName !== null && !is_string($vulnName)) {
            throw new \InvalidArgumentException("Vulnerability name must be a valid string.");
        }

        if ($vulnName) {
            $this->cachedVulnerabilities->removeVulnerability($vulnName);
        } else {
            $this->cachedVulnerabilities->clear();
        }

        foreach ($this->children as $child) {
            $child->clearCache($vulnName);
        }
    }

    /**
     * Find first but deepest element (with no children) in the tree which matches the request
     * @param Request $request
     * @return VulnerableElement|ConditionalVulnerableElement
     */
    public function match(Request $request = null)
    {
        if ($this->children->count()) {
            foreach ($this->children as $child) {
                if ($matched = $child->match($request)) {
                    return $matched;
                }
            }
        }
        return $this;
    }

    /**
     * @param $name
     * @param int $flags
     * @return null|Vulnerability
     */
    public function getComputedVulnerability($name, $flags = 0)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException("Vulnerability name must be a string. Provided: '$name'");
        }

        $computeOnlyRoot = (boolean) ($flags & self::COMPUTE_ONLY_ROOT);

        if ($computeOnlyRoot && $this->getParent()) {
            return $this->getParent()->getComputedVulnerability($name, $flags);
        }

        if ($this->cachedVulnerabilities->hasOwnVulnerability($name)) {
            return $this->cachedVulnerabilities->get($name);
        }

        if ($this->vulnerabilitySet->hasOwnVulnerability($name)) {
            $vuln = $this->vulnerabilitySet->get($name);

        } else {
            $parent = $this->getParent();

            if ($parent) {
                $vuln = $this->getParent()->getComputedVulnerability($name, $flags);

            } else if ($this->host) {
                $vuln = $this->host->getParentVulnerability($name, null, null, $computeOnlyRoot);
                if ($vuln === null) {
                    $vuln = VulnerabilityFactory::instance()->create($name, false);
                }

            } else {
                $vuln = VulnerabilityFactory::instance()->create($name, false);
            }
        }

        if ($vuln) {
            if (!$vuln->isTargetedAt($this->targets)) {
                $this->cachedVulnerabilities->set(false, $name);
                $vuln = false;

            } else {
                $this->cachedVulnerabilities->set($vuln);
            }

        } else {
            $this->cachedVulnerabilities->set(false, $name);
            $vuln = false;
        }

        return $vuln;
    }

    /**
     * @param int $flags
     * @return array|ArrayObject <Vulnerability>|Vulnerability[]
     */
    public function getComputedVulnerabilities($flags = 0)
    {
        $vulnNames = VulnerabilityFactory::instance()->getAllVulnerabilityNames();
        $computedVulnerabilities = new ArrayObject();

        /** @var Vulnerability $vuln */
        foreach ($vulnNames as $vulnName) {
            $computedVulnerabilities[$vulnName] = $this->getComputedVulnerability($vulnName, $flags);
        }

        return $computedVulnerabilities;
    }

    /**
     * @param $name
     * @return bool|null|\VulnModule\Vulnerability
     */
    public function hasVulnerabilityInTree($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException("Vulnerability name must be a string. Provided: '$name'");
        }

        if ($this->vulnerabilitySet->hasOwnVulnerability($name)) {
            return true;

        } else {
            if ($this->getParent()) {
                return $this->getParent()->hasVulnerabilityInTree($name);

            } else {
                return false;
            }
        }
    }

    /**
     * @param VulnerabilityHost $host
     */
    public function setHost(VulnerabilityHost $host)
    {
        $this->host = $host;
        if ($host) {
            $this->setTargets($host::$targets);

        } else {
            $this->setTargets(null);
        }
    }

    /**
     * @return VulnerabilityHost
     */
    public function getHost()
    {
        return $this->host;
    }

    public function getPath()
    {
        $path = $this->getParent() ? $this->getParent()->getPath() . '->' : ($this->host ? $this->host->getPath() . '|' : '||');
        return  $path . ($this->name ?: ($this->getParent() ? $this->getParent()->getChildIndex($this) : '0'));
    }

    public function getElementByPath($path, $createOnMissing = true)
    {
        $parts = preg_split('/->/', $path);

        if (isset($this->children[$parts[0]]) || $createOnMissing) {
            if (!($element = $this->getChildByName($parts[0])) || !($element = $this->children[$parts[0]])) {
                $element = new VulnerableElement();
                $this->addChild($element);
            }

            unset($parts[0]);

            if (count($parts)) {
                return $element->getElementByPath(implode('->', $parts), $createOnMissing);

            } else {
                return $element;
            }
        }

        return null;
    }

    public function getDefaultElement()
    {
        if ($this->hasChildren()) {
            foreach ($this->children as $child) {
                if ($child instanceof ConditionalVulnerableElement) {
                    if (!$child->hasConditions()) {
                        return $child->getDefaultElement();
                    }
                } else {
                    return $child->getDefaultElement();
                }
            }
        }

        return $this;
    }

    protected function requestClearCache()
    {
        if ($this->getParent()) {
            $this->getParent()->requestClearCache();

        } else if ($this->host) {
            $this->host->clearCache();

        } else {
            $this->clearCache();
        }
    }

    /**
     * @return VulnerableElement[]|ArrayObject
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param VulnerableElement[]|ArrayObject $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
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

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
    }

    private function getChildIndex(VulnerableElement $child)
    {
        if ($this->hasChild($child)) {
            return $this->children->findIndex($child);
        }
        return -1;
    }

    public function getChildByName($name)
    {
        if (!$name || !is_scalar($name)) {
            return null;
        }
        foreach ($this->children as $child) {
            if ($child->getName() && $child->getName() == $name) {
                return $child;
            }
        }

        return null;
    }

    /**
     * @return int
     */
    public function getTargets()
    {
        return $this->targets;
    }

    /**
     * @param array|mixed $targets
     */
    public function setTargets($targets)
    {
        if ($this->targets == $targets) {
            return;
        }

        $this->targets = $targets;

        if ($this->hasChildren()) {
            foreach ($this->children as $child) {
                $child->setTargets($targets);
            }
        }
    }

    /**
     * @return VulnerabilityHost|Context|Field|null
     */
    public function getTreeHost()
    {
        if ($this->host) {
            return $this->host;
        }

        if ($this->parent) {
            return $this->parent->getTreeHost();
        }

        return null;
    }
}