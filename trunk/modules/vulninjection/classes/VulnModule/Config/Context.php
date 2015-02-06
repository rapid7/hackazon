<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 30.11.2014
 * Time: 16:34
 */


namespace VulnModule\Config;


use App\Pixie;
use App\Pixifier;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use VulnModule\Config\URL\URL;
use VulnModule\DataType\ArrayObject;
use VulnModule\Exception\MissingContextVulnerabilityException;
use VulnModule\Exception\MissingFieldVulnerabilityException;
use VulnModule\Exception\MissingVulnerabilityInTreeException;
use VulnModule\Config\Annotations as Vuln;

/**
 * Represents the context for vulnerabilities
 * @package VulnModule\Config
 */
class Context extends VulnerabilityHost
{
    // Context types
    const TYPE_STANDARD = 'standard';   // Any intermediary context
    // Top-level context (although there may be nested app-level contexts, but above the controller)
    const TYPE_APPLICATION = 'application';
    const TYPE_CONTROLLER = 'controller';
    const TYPE_ACTION = 'action';

    // Types of storage role
    const STORAGE_ROLE_ROOT = 'root';
    const STORAGE_ROLE_CHILD = 'child';

    const TECH_GENERIC = 'generic';
    const TECH_WEB = 'web';
    const TECH_GWT = 'gwt';
    const TECH_AMF = 'amf';
    const TECH_REST = 'rest';

    /**
     * @var Context[]|ArrayObject<Context2>
     */
    protected $children;

    /**
     * @var Field[]|ArrayObject<Field>
     */
    protected $fields;

    /**
     * @var Context
     */
    protected $parent;

    /**
     * @var string
     */
    protected $type = self::TYPE_STANDARD;

    /**
     * @var string
     */
    protected $storageRole = self::STORAGE_ROLE_CHILD;

    /**
     * @var string
     */
    protected $technology = self::TECH_GENERIC;

    /**
     * @var string
     */
    protected $routeDescription = '';

    /**
     * @var string Name of the controller, if differs from context name
     */
    protected $mappedTo;

    /**
     * @var Pixie
     */
    protected $pixie;

    /**
     * @param string $name
     * @param VulnerableElement $vulnTree
     * @param string $type
     * @param string $storageRole
     */
    function __construct($name = null, VulnerableElement $vulnTree = null, $type = self::TYPE_STANDARD, $storageRole = self::STORAGE_ROLE_CHILD)
    {
        parent::__construct($name, $vulnTree);
        $this->children = new ArrayObject();
        $this->fields = new ArrayObject();
        $this->type = $type;
        $this->storageRole = $storageRole;
        $this->pixie = Pixifier::getInstance()->getPixie();
    }

    public static function getRoutes()
    {
        static $routeNames = null;

        if (!$routeNames) {
            $routeNames = array_keys(Pixifier::getInstance()->getPixie()->router->getRoutes());
        }

        return $routeNames;
    }

    /**
     * @param Context $child
     * @throws \Exception
     */
    public function addChild(Context $child)
    {
        if (!$child || $this->children->contains($child)) {
            return;
        }

        // Ensure we add only allowed sub-contexts
        if (!$this->canContainSubtree($child)) {
            throw new \Exception('Invalid context hierarchy structure.');
        }

        if ($child->getParent()) {
            $child->getParent()->removeChild($child);
        }

        $this->children->append($child);
        $child->setParent($this);

        if ($this->technology != Context::TECH_GENERIC && $this->technology != $child->technology) {
            $child->setTechnology($this->technology);
        }
    }

    /**
     * @param Context $child
     * @return Context
     */
    public function removeChild(Context $child)
    {

        $this->children->removeElement($child);
        $child->setParent(null);
        return $child;
    }

    /**
     * @param Context $child
     * @return bool
     */
    public function hasChild(Context $child)
    {
        if (!$child) {
            return false;
        }

        return $this->children->contains($child);
    }

    /**
     * @param string $childName
     * @return bool
     */
    public function hasChildByName($childName)
    {
        if (!$childName || !is_string($childName)) {
            return false;
        }

        return $this->getChildByName($childName) !== null;
    }

    /**
     * @param Field $field
     * @return bool
     */
    public function hasField(Field $field)
    {
        if (!$field) {
            return false;
        }

        return $this->fields->contains($field);
    }

    /**
     * @param Field $field
     * @return Field
     */
    public function removeField(Field $field)
    {
        $this->fields->removeElement($field);
        $field->setParent(null);
        return $field;
    }

    /**
     * @return Context
     */
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

        if (!($parent instanceof Context)) {
            throw new \InvalidArgumentException("Context can only be a child of other context.");
        }



        if ($this->parent && $this->parent->hasChild($this)) {
            $this->parent->removeChild($this);
        }

        $this->parent = $parent;
    }

    /**
     * @return Context[]|ArrayObject<Context2>
     */
    public function getChildrenArray()
    {
        return $this->children->getArrayCopy();
    }

    /**
     * @return Context[]|\ArrayIterator<Context2>
     */
    public function getChildrenIterator()
    {
        return $this->children->getIterator();
    }

    /**
     * @param Field $field
     */
    public function addField(Field $field)
    {
        if (!$field || $this->fields->contains($field)) {
            return;
        }

        if ($field->getParent()) {
            $field->getParent()->removeField($field);
        }

        $this->fields->append($field);
        $field->setParent($this);
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
     * @param $name
     * @param VulnerabilityHost $startNode
     * @param FieldDescriptor $descriptor
     * @param bool $onlyRoot
     * @return null|\VulnModule\Vulnerability
     * @throws MissingContextVulnerabilityException
     * @throws MissingFieldVulnerabilityException
     */
    public function _getParentVulnerabilityFromParentNode(
        $name, VulnerabilityHost $startNode = null, FieldDescriptor $descriptor = null,
        $onlyRoot = false)
    {
        if ($descriptor) {
            foreach ($this->fields as $field) {
                if ($field->matchesToDescriptor($descriptor)) {
                    if ($field->hasOwnVulnerability($name, $onlyRoot)) {
                        return $field->getVulnerability($name, $onlyRoot);
                    }
                }
            }

            if ($this->getParent()) {
                return $this->getParent()->getParentVulnerability($name, $startNode, $descriptor, $onlyRoot);
            } else {
                throw new MissingFieldVulnerabilityException();
            }

        } else {
            try {
                return parent::_getParentVulnerabilityFromParentNode($name, $startNode, null, $onlyRoot);

            } catch (MissingVulnerabilityInTreeException $e) {
                throw new MissingContextVulnerabilityException("", 0, $e);
            }
        }
    }

    /**
     * @param FieldDescriptor $descriptor
     * @return null|Field
     */
    public function getMatchingField(FieldDescriptor $descriptor)
    {
        if ($descriptor === null) {
            return null;
        }

        foreach ($this->fields as $field) {
            if ($field->matchesToDescriptor($descriptor)) {
                return $field;
            }
        }

        return null;
    }

    /**
     * @param FieldDescriptor $descriptor
     * @return bool
     */
    public function hasMatchingField(FieldDescriptor $descriptor)
    {
        return $this->getMatchingField($descriptor) !== null;
    }

    public function getOrCreateMatchingField(FieldDescriptor $descriptor)
    {
        $field = $this->getMatchingField($descriptor);
        if (!$field) {
            $field = new Field($descriptor->getName(), null, $descriptor->getSource());
            $this->addField($field);
        }

        return $field;
    }

    /**
     * Get a set of context types from all children and the element itself
     * @return array
     */
    public function getContextTypesFromTree()
    {
        $types = [$this->type];

        foreach ($this->children as $child) {
            $types = array_merge($types, $child->getContextTypesFromTree());
        }

        return array_unique($types);
    }

    /**
     * @param array $childrenTypes
     * @return bool
     */
    public function canContainChildrenOfTypes(array $childrenTypes)
    {
        $parentTypes = $this->getSelfAndAllParentTypes();

        if (in_array(self::TYPE_ACTION, $childrenTypes) && in_array(self::TYPE_ACTION, $parentTypes)) {
            return false;

        } else if (count(array_intersect([self::TYPE_APPLICATION, self::TYPE_CONTROLLER], $childrenTypes))
            && count(array_intersect([self::TYPE_ACTION, self::TYPE_CONTROLLER], $parentTypes))
        ) {
            return false;
        }

        return true;
    }

    public function canContainSubtree(Context $child)
    {
        $types = $child->getContextTypesFromTree();
        return $this->canContainChildrenOfTypes($types);
    }

    /**
     * @return array
     */
    public function getSelfAndAllParentTypes()
    {
        $types = [$this->type];
        if ($this->parent) {
            $types = array_merge($types, [$this->parent->getType()]);
        }
        return array_unique($types);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getStorageRole()
    {
        return $this->storageRole;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param string $storageRole
     */
    public function setStorageRole($storageRole)
    {
        $this->storageRole = $storageRole;
    }

    /**
     * @return Context[]|ArrayObject
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return Field[]|ArrayObject
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function hasChildren()
    {
        return count($this->children) > 0;
    }

    public function hasFields()
    {
        return count($this->fields) > 0;
    }

    /**
     * @param $name
     * @return null|Context
     */
    public function getChildByName($name)
    {
        foreach ($this->children as $child) {
            if ($child->getName() === $name) {
                return $child;
            }
        }

        return null;
    }

    /**
     * @param $name
     * @return Context
     * @throws \Exception
     */
    public function getOrCreateChildByName($name)
    {
        $child = $this->getChildByName($name);
        if (!$child) {
            $child = new Context($name);
            $this->addChild($child);
        }

        return $child;
    }

    /**
     * Returns first matching field or null
     * @param $name
     * @param string $source
     * @return null|Field
     */
    public function getField($name, $source = FieldDescriptor::SOURCE_ANY)
    {
        foreach ($this->fields as $field) {
            if ($field->getName() === $name
                && ($source === FieldDescriptor::SOURCE_ANY || $source === $field->getSource())
            ) {
                return $field;
            }
        }

        return null;
    }

    /**
     * Returns all fields that match arguments.
     * @param null $name
     * @param string $source
     * @return array|Field[]
     */
    public function getMatchingFields($name = null, $source = FieldDescriptor::SOURCE_ANY)
    {
        $result = [];

        foreach ($this->fields as $field) {
            if (($name === null || $field->getName() === $name)
                && ($source === FieldDescriptor::SOURCE_ANY || $source === $field->getSource())
            ) {
                $result[] = $field;
            }
        }

        return $result;
    }

    public static function getStorageRoles()
    {
        return [
            self::STORAGE_ROLE_CHILD,
            self::STORAGE_ROLE_ROOT
        ];
    }

    public static function getTypes()
    {
        return [
            self::TYPE_APPLICATION,
            self::TYPE_CONTROLLER,
            self::TYPE_ACTION,
            self::TYPE_STANDARD
        ];
    }

    public static function getTechnologies()
    {
        return [
            self::TECH_GENERIC,
            self::TECH_AMF,
            self::TECH_GWT,
            self::TECH_WEB,
            self::TECH_REST
        ];
    }

    public static function getTechnologiesLabels()
    {
        return [
            self::TECH_GENERIC => 'Generic',
            self::TECH_AMF => 'AMF',
            self::TECH_GWT => 'GWT',
            self::TECH_WEB => 'Web',
            self::TECH_REST => 'REST'
        ];
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        parent::loadValidatorMetadata($metadata);

        $metadata->addPropertyConstraints('storageRole', [
            new Constraints\NotBlank(['message' => 'Storage role cannot be blank.']),
            new Constraints\Choice([
                'choices' => self::getStorageRoles(),
                'multiple' => false
            ])
        ]);

        $metadata->addPropertyConstraints('type', [
            new Constraints\NotBlank(['message' => 'Context type cannot be blank.']),
            new Constraints\Choice([
                'choices' => self::getTypes(),
                'multiple' => false
            ])
        ]);
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param $path
     * @param bool $createOnMissing
     * @return null|Context|Field
     */
    public function getElementByPath($path, $createOnMissing = true)
    {
        //var_dump($path);exit;

        $parts = preg_split('/\|/', $path);
        $contextPart = $parts[0];
        $fieldPart = $parts[1];
        $vulnPart = $parts[2];

        if ($contextPart) {
            $contextParts = preg_split('/->/', $contextPart);
            /** @var Context|null $context */
            $context = null;

            if ($this->hasChildByName($contextParts[0]) || $createOnMissing) {
                if (!($context = $this->getChildByName($contextParts[0]))) {
                    $context = new Context($contextParts[0]);
                    $this->addChild($context);
                }

                unset($contextParts[0]);
                unset($parts[0]);

                if ($context) {
                    if (count($contextParts) || $vulnPart || $fieldPart) {
                        array_unshift($parts, implode('->', $contextParts));
                        return $context->getElementByPath(implode('|', $parts), $createOnMissing);

                    } else {
                        return $context;
                    }

                } else {
                    return null;
                }

            } else {
                return null;
            }

        } else if ($fieldPart) {
            $fieldParts = preg_split('/:/', $fieldPart);
            $name = $fieldParts[0];
            $source = $fieldParts[1] ?: FieldDescriptor::SOURCE_ANY;
            $descriptor = new FieldDescriptor($name, $source);

            if ($this->hasMatchingField($descriptor) || $createOnMissing) {
                if (!($field = $this->getMatchingField($descriptor))) {
                    $field = new Field($name, null, $source);
                    $this->addField($field);
                }

                if ($vulnPart || $vulnPart === '0' || $vulnPart === 0) {
                    return $field->getVulnElementByPath($vulnPart, $createOnMissing);

                } else {
                    return $field;
                }

            } else {
                return null;
            }

        } else if ($vulnPart) {
            return $this->getVulnElementByPath($path, $createOnMissing);

        } else {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getTechnology()
    {
        return $this->technology;
    }

    /**
     * @return string
     */
    public function getTechnologyLabel()
    {
        $labels = self::getTechnologiesLabels();
        return $labels[$this->technology];
    }

    /**
     * @param string $technology
     */
    public function setTechnology($technology)
    {
        $this->technology = $technology;

        if ($this->technology != Context::TECH_GENERIC && $this->hasChildren()) {
            foreach ($this->children as $child) {
                $child->setTechnology($this->technology);
            }
        }
    }

    public function getURL()
    {
        $routeName = null;
        $params = [];
        $metadata = null;

        /** @var ContextMetadataFactory $metadataFactory */
        $metadataFactory = $this->pixie->container['vulnerability.context_metadata_factory'];

        if ($this->type == self::TYPE_CONTROLLER) {
            $metadata = $metadataFactory->getMetadata($this->getMappedTo(), null, $this->technology);
            $params = ['controller' => $this->getMappedTo()];

        } else if ($this->type == self::TYPE_ACTION && $this->getParent()) {
            $metadata = $metadataFactory->getMetadata($this->getParent()->getMappedTo(), $this->getMappedTo(), $this->technology);
            $params = [
                'controller' => $this->getParent() ? $this->getParent()->getMappedTo() : false,
                'action' => $this->getMappedTo()
            ];
        }

        if ($this->technology == self::TECH_REST) {
            if (!$metadata) {
                $metadata = new ContextMetadata();
            }

            if (!$metadata->getRoute()) {
                $metadata->setRoute('rest');
            }
        }

        /** @var Vuln\Route $annotation */
        if ($metadata) {
            if ($metadata->getRoute()) {
                $routeName = $metadata->getRoute();

            } else {
                $routeName = 'default';
            }

            if (count($metadata->getRouteParams())) {
                $params = array_merge($params, $metadata->getRouteParams());
            }

            if ($metadata->getDescription()) {
                $this->routeDescription = $metadata->getDescription();
            }
        }

        if ($this->technology == self::TECH_REST) {
            if (!$params['controller']) {
                $params['controller'] = false;
            }
        }

        if ($routeName && in_array($this->technology, [self::TECH_GENERIC, self::TECH_WEB, self::TECH_REST])) {
            $url = $this->pixie->router->generateUrl($routeName, $params, false, 'http', false);
            if ($this->technology == self::TECH_REST && $params['action']) {
                $url .= ' [ ' . strtoupper($params['action']) . ' ]';
            }
            return $url;
        }

        $url = $this->getParent() ? $this->getParent()->getURL() : new URL();
        if (!is_object($url)) {
            $parentUrl = $url;
            $url = new URL();
            $url->addSegment($parentUrl);
        }

        $url->setTechnology($this->getTechnology());

        if ($this->getMappedTo() == 'default' && !$this->parent) {
            $url->addSegment('/');

        } else {
             if ($this->type == self::TYPE_CONTROLLER) {
                 $url->setService($this->getMappedTo());

             } else if ($this->type == self::TYPE_ACTION) {
                 $url->setMethod($this->getMappedTo());

             } else {
                 $url->addSegment($this->getMappedTo());
             }
        }

        return $url;
    }

    /**
     * @return string
     */
    public function getRouteDescription()
    {
        return $this->routeDescription;
    }

    /**
     * @return string
     */
    public function getMappedTo()
    {
        return $this->mappedTo ?: $this->name;
    }

    /**
     * @param string $mappedTo
     */
    public function setMappedTo($mappedTo)
    {
        $this->mappedTo = $mappedTo;
    }
}