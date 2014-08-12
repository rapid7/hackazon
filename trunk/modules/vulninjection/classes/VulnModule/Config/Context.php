<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 07.08.2014
 * Time: 11:50
 */


namespace VulnModule\Config;
use App\Debug;
use App\Helpers\ArraysHelper;
use VulnModule\DataType\ArrayObject;

/**
 * Class Context
 * Holds the config for certain level
 * @package PHPixie\Config
 */
class Context
{
    const TYPE_DEFAULT = 0;     // Usual context
    const TYPE_FORM = 1;        // Form context

    public static $vulnTypes = ['xss', 'sql', 'csrf'];

    public static $contextTypes = [
        self::TYPE_DEFAULT,
        self::TYPE_FORM
    ];

    /**
     * Type of this context. It influences on some functionality.
     */
    protected $type;

    /**
     * Name of the context
     * @var string
     */
    protected $name;

    /**
     * @var Context
     */
    protected $parent = null;

    /**
     * Single fields in the request.
     * For example, "q" for search request.
     * @var array|ArrayObject
     */
    protected $fields = null;

    /**
     * @var array|ArrayObject
     */
    protected $vulnerabilities = null;

    /**
     * Child contexts, including controller, form and custom contexts.
     * @var null|Context[]
     */
    protected $children = null;

    /**
     * Constructs context.
     * @param string $name
     * @param array $children
     * @param int $type
     */
    public function __construct($name = '', array $children = [], $type = self::TYPE_DEFAULT)
    {
        $this->children = $children;
        $this->setName($name);
        $this->setType($type);
    }

    /**
     * Factory method to create context tree from config array.
     * @param $name
     * @param $parent
     * @param array $data
     * @param int $type
     * @return Context
     */
    public static function createFromData($name, array $data = [], $parent = null, $type = self::TYPE_DEFAULT)
    {
        $context = new Context($name, [], $type);
        $context->setParent($parent);
        $context->setFields($data['fields']);
        $context->setVulnerabilities($data['vulnerabilities']);

        // Add contexts forms contexts
        if (is_array($data['forms'])) {
            foreach ($data['forms'] as $formName => $formData) {
                self::checkValidName($formName);
                $formContext = self::createFromData($formName, $formData, $context, self::TYPE_FORM);
                $context->addContext($formContext);
            }
        }

        // Add contexts sub-contexts
        $blocks = ['contexts', 'actions'];
        foreach ($blocks as $block) {
            if (is_array($data[$block])) {
                foreach ($data[$block] as $subContextName => $subContextData) {
                    self::checkValidName($subContextName);
                    $subContext = self::createFromData($subContextName, $subContextData, $context, self::TYPE_DEFAULT);
                    $context->addContext($subContext);
                }
            }
        }

        return $context;
    }

    /**
     * Returns parent of this context.
     * @return Context
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Returns children of this context.
     * @return null|Context[]
     */
    public function getChildren()
    {
        return $this->children;
    }


    /**
     * Sets parent of this context.
     * @param Context $context
     * @return $this
     */
    public function setParent(Context $context = null)
    {
        $this->parent = $context;
        return $this;
    }

    /**
     * Sets fields which are to be filtered on vulnerabilities.
     * @param array $fields
     * @return $this
     */
    public function setFields($fields = [])
    {
        $parentFields = $this->parent ? $this->parent->getFields() : [];
        $this->fields = ArraysHelper::arrayMergeRecursiveDistinct($parentFields, $fields);

        return $this;
    }

    /**
     * Sets vulnerabilities of this context.
     * @param array $vulnerabilities
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setVulnerabilities($vulnerabilities = [])
    {
        // Clear current vulnerabilities
        $this->vulnerabilities = [];

        if (!is_array($vulnerabilities)) {
            $vulnerabilities = (array) $vulnerabilities;
        }

        foreach ($vulnerabilities as $type => $vulnerability) {
            $this->checkValidType($type);
            $this->vulnerabilities[$type] = $vulnerability;
        }

        $parentVulns = $this->parent ? $this->parent->getVulnerabilities() : [];
        $this->vulnerabilities = ArraysHelper::arrayMergeRecursiveDistinct($parentVulns, $this->vulnerabilities);

        return $this;
    }

    /**
     * Adds context to the collection.
     * @param Context $context
     */
    public function addContext(Context $context)
    {
        $this->children[$context->getName()] = $context;
        $context->parent = $this;
    }

    /**
     * Returns name of the context.
     * @return string
     */
    private function getName()
    {
        return $this->name;
    }

    /**
     * Sets context name.
     * @param $name
     * @return $this
     * @throws \InvalidArgumentException
     */
    private function setName($name)
    {
        self::checkValidName($name);
        $this->name = $name;
        return $this;
    }

    /**
     * Set type of the context.
     * @param $type
     * @return $this
     */
    private function setType($type)
    {
        $this->checkValidType($type);
        $this->type = $type;
        return $this;
    }

    /**
     * Check that given $type is correct.
     * @param $type
     * @throws \InvalidArgumentException
     */
    public static function checkValidType($type)
    {
        if (!in_array((int)$type, self::$contextTypes)) {
            throw new \InvalidArgumentException('Invalid vulnerability type "' . $type
                . '". Valid types are: ' . implode(', ', self::$contextTypes));
        }
    }

    /**
     * Checks that context names does not contain dots.
     * @param $name
     * @throws \InvalidArgumentException
     */
    public static function checkValidName($name)
    {
        if (preg_match('/\./', $name)) {
            throw new \InvalidArgumentException(
                'Vulnerability context name must not contain dots, as it serves as name separator.');
        }
    }

    /**
     * Finds given context in all tree.
     * @param Context $context
     * @return bool
     */
    public function findInTree(Context $context)
    {
        if (!is_array($this->children) || !count($this->children)) {
            return false;
        }

        foreach ($this->children as $child) {
            if ($child === $context || $child->findInTree($context)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Search context in tree by given path.
     *
     * @param $path
     * @return null
     */
    public function findByPath($path)
    {
        if (!is_array($path)) {
            $pathParts = preg_split('/\./', $path, -1, PREG_SPLIT_NO_EMPTY);
        } else {
            $pathParts = $path;
        }

        if (!count($pathParts)) {
            return null;
        }

        $firstPart = array_shift($pathParts);

        if (array_key_exists($firstPart, $this->children)) {
            $context = $this->children[$firstPart];

            if (!count($pathParts)) {
                return $context;
            }

            return $context->findByPath($path);
        }

        return null;
    }

    /**
     * @return array|ArrayObject
     */
    public function getVulnerabilities()
    {
        return $this->vulnerabilities;
    }

    /**
     * @return array|ArrayObject
     */
    public function getFields()
    {
        return $this->fields;
    }
}