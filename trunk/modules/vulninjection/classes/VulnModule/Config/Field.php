<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 01.12.2014
 * Time: 10:21
 */


namespace VulnModule\Config;


use App\Pixifier;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use VulnModule\Vulnerability;

/**
 * Request field
 * @package VulnModule\Config
 */
class Field extends VulnerabilityHost
{
    /**
     * @var string
     */
    protected $source;

    /**
     * @var FieldDescriptor
     */
    protected $descriptor;

    /**
     * @var Context
     */
    protected $parent;

    public static $targets = [
        Vulnerability::TARGET_FIELD
    ];

    /**
     * @param string $name
     * @param string $source
     * @param VulnerableElement $vulnTree
     */
    function __construct($name = null, VulnerableElement $vulnTree = null, $source = FieldDescriptor::SOURCE_ANY)
    {
        parent::__construct($name, $vulnTree);
        $this->source = strtolower($source);
    }

    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param VulnerabilityHost $parent
     */
    public function setParent(VulnerabilityHost $parent)
    {
        if ($this->parent === $parent) {
            return;
        }

        if ($this->parent && $this->parent->hasField($this)) {
            $this->parent->removeField($this);
        }

        $this->parent = $parent;
    }

    public function getRequest()
    {
        if ($this->request) {
            return $this->request;
        } else {
            if ($this->parent) {
                return $this->parent->getRequest();
            } else {
                return Pixifier::getInstance()->getPixie()->http_request();
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getParentVulnerability($name, VulnerabilityHost $startNode = null, FieldDescriptor $descriptor = null,
                                           $onlyRoot = false)
    {
        if ($startNode === null && $descriptor === null) {
            $startNode = $this;
            $descriptor = $this->getDescriptor();
        }

        return parent::getParentVulnerability($name, $startNode, $descriptor, $onlyRoot);
    }

    /**
     * @param FieldDescriptor $descriptor
     * @return bool
     */
    public function matchesToDescriptor(FieldDescriptor $descriptor)
    {
        if ($descriptor === null) {
            return false;
        }

        return $this->getDescriptor()->conformsTo($descriptor);
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        parent::loadValidatorMetadata($metadata);

        $metadata->addPropertyConstraints('source', [
            //new Constraints\NotNull("Source cannot be empty."),
            new Constraints\NotBlank(['message' => "Source cannot be empty."]),
            new Constraints\Choice([
                'choices' => FieldDescriptor::getSources()
            ])
        ]);
    }

    /**
     * @return FieldDescriptor
     */
    public function getDescriptor()
    {
        if (!$this->descriptor) {
            $this->descriptor = new FieldDescriptor($this->name, $this->source);
        }
        return $this->descriptor;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    public function getPath()
    {
        $path = $this->getParent() ? $this->getParent()->getPath() : '';
        return $path . '|' . $this->getName() . ':' . $this->getSource();
    }
}