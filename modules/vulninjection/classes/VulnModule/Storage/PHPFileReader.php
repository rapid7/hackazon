<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 27.11.2014
 * Time: 11:25
 */


namespace VulnModule\Storage;


use VulnModule\Config\ConditionalVulnerableElement;
use VulnModule\Config\ConditionFactory;
use VulnModule\Config\ConditionSet;
use VulnModule\Config\Context;
use VulnModule\Config\Field;
use VulnModule\Config\FieldDescriptor;
use VulnModule\Config\VulnerableElement;
use VulnModule\VulnerabilityFactory;
use VulnModule\VulnerabilitySet;

/**
 * Reads the vulnerability config
 * @package VulnModule\Context
 */
class PHPFileReader implements IReader
{
    /**
     * @var string
     */
    protected $basePath;

    /**
     * Reference configs path
     * @var string
     */
    protected $samplePath;

    function __construct($basePath = '')
    {
        $this->basePath = preg_replace('/[\\\\\/]+$/i', '', $basePath);
        $this->samplePath = $this->basePath . '/../vuln.sample';
    }

    /**
     * @param $name
     * @return null|Context
     */
    public function read($name)
    {
        $fullPath = ($this->basePath ? $this->basePath . '/' . $name : $name) . '.php';

        if (!file_exists($fullPath) || !is_file($fullPath)) {
            $fullPath = ($this->samplePath ? $this->samplePath . '/' . $name : $name) . '.php';
            if (!file_exists($fullPath) || !is_file($fullPath)) {
                return null;
            }
        }

        /** @noinspection PhpIncludeInspection */
        $config = @include $fullPath;
        $config = is_array($config) ? $config : [];

        return $this->buildContextFromArray($config, basename($fullPath, '.php'), Context::STORAGE_ROLE_ROOT);
    }

    /**
     * @param array $config
     * @param null $name
     * @param string $storageRole
     * @return Context
     * @throws \Exception
     */
    public function buildContextFromArray(array $config, $name = null, $storageRole = Context::STORAGE_ROLE_CHILD)
    {
        $type = in_array($config['type'], Context::getTypes()) ? $config['type'] : Context::TYPE_STANDARD;
        $technology = in_array($config['technology'], Context::getTechnologies()) ? $config['technology'] : Context::TECH_GENERIC;
        $context = new Context($name, null, $type, $storageRole);
        $context->setTechnology($technology);
        $context->setMappedTo($config['mapped_to'] ?: '');

        if (is_array($config['fields'])) {
            $fields = $config['fields'];

            // Iterate all fields and create a rule set
            foreach ($fields as $fieldData ) {
                $vulnElement = $this->buildVulnerabilityElementFromArray($fieldData['vulnerabilities']);

                $source = $fieldData['source'] ?: FieldDescriptor::SOURCE_ANY;

                if (!in_array($source, FieldDescriptor::getSources())) {
                    throw new \InvalidArgumentException("Invalid source for field '{$fieldData['name']}': " . $source);
                }

                $field = new Field($fieldData['name'], $vulnElement, $source);
                $context->addField($field);
            }
        }

        if (is_array($config['children'])) {
            foreach ($config['children'] as $contextName => $contextData) {
                $child = $this->buildContextFromArray($contextData, $contextName);
                $context->addChild($child);
            }
        }


        if (is_array($config['vulnerabilities'])) {
            $vulnElement = $this->buildVulnerabilityElementFromArray($config['vulnerabilities']);
            $context->setVulnTree($vulnElement);
        }

        return $context;
    }

    /**
     * Builds vulnerability set from list of vulns
     * @param $vulnList
     * @return VulnerabilitySet
     */
    protected function buildVulnerabilitySetFromArray($vulnList)
    {
        $resultSet = new VulnerabilitySet();
        if (!is_array($vulnList) || empty($vulnList)) {
            return $resultSet;
        }

        $factory = VulnerabilityFactory::instance();

        foreach ($vulnList as $name => $data) {
            if (!$factory->exists($name)) {
                continue;
            }

            $vuln = $factory->create($name);
            $vuln->fillFromArray($data);

            $resultSet->set($vuln);
        }

        return $resultSet;
    }

    protected function buildVulnerabilityElementFromArray($data, $conditional = false)
    {
        $vulnerabilities = $this->buildVulnerabilitySetFromArray($data['vuln_list']);

        if ($conditional) {
            $vulnElement = new ConditionalVulnerableElement($vulnerabilities);

            if (is_array($data['conditions'])) {
                $factory = ConditionFactory::instance();
                $conditionSet = new ConditionSet();

                foreach ($data['conditions'] as $name => $conditionData) {
                    $condition = $factory->create($name);
                    $condition->fillFromArray($conditionData);
                    $conditionSet->addCondition($condition);
                }

                $vulnElement->setConditions($conditionSet);
            }

        } else {
            $vulnElement = new VulnerableElement($vulnerabilities);
        }

        if ($data['name']) {
            $vulnElement->setName($data['name']);
        }

        if (is_array($data['children'])) {
            foreach ($data['children'] as $childData) {
                $child = $this->buildVulnerabilityElementFromArray($childData, true);
                $vulnElement->addChild($child);
            }
        }
        return $vulnElement;
    }

    /**
     * @return array
     */
    public function getOwnContextNames()
    {
        return $this->getContextNames($this->basePath);
    }

    /**
     * @return array
     */
    public function getReferenceContextNames()
    {
        return $this->getContextNames($this->samplePath);
    }

    /**
     * @return array
     */
    public function getAllContextNames()
    {
        $allContextNames = array_unique(array_merge($this->getOwnContextNames(), $this->getReferenceContextNames()));
        sort($allContextNames);
        return $allContextNames;
    }

    private function getContextNames($configDir)
    {
        $dirs = [];

        foreach (new \DirectoryIterator($configDir) as $fileInfo) {
            if ($fileInfo->isDot() || $fileInfo->getExtension() != 'php' || !$fileInfo->isReadable()) {
                continue;
            }
            $dirs[] = $fileInfo->getBasename('.php');
        }

        return $dirs;
    }
}