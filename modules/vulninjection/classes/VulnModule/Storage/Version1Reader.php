<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 27.11.2014
 * Time: 11:25
 */


namespace VulnModule\Storage;


use VulnModule\Config\Context;
use VulnModule\Config\Field;
use VulnModule\Config\FieldDescriptor;
use VulnModule\Config\VulnerableElement;
use VulnModule\Vulnerability\ArbitraryFileUpload;
use VulnModule\Vulnerability\CSRF;
use VulnModule\Vulnerability\OSCommand;
use VulnModule\Vulnerability\Referer;
use VulnModule\Vulnerability\RemoteFileInclude;
use VulnModule\Vulnerability\SQL;
use VulnModule\Vulnerability\XMLExternalEntity;
use VulnModule\Vulnerability\XSS;
use VulnModule\VulnerabilityFactory;
use VulnModule\VulnerabilitySet;

/**
 * Reads the old format of vulnerability config
 * @package VulnModule\Context
 */
class Version1Reader implements IReader
{
    /**
     * @var string
     */
    protected $basePath;

    function __construct($basePath = '')
    {
        $this->basePath = preg_replace('/[\\\\\/]+$/i', '', $basePath);
    }

    /**
     * @param $name
     * @return null|Context
     */
    public function read($name)
    {
        $fullPath = ($this->basePath ? $this->basePath . '/' . $name : $name) . '.php';

        if (!file_exists($fullPath) || !is_file($fullPath)) {
            return null;
        }

        /** @noinspection PhpIncludeInspection */
        $config = @include $fullPath;
        $config = is_array($config) ? $config : [];

        return $this->buildFromArray($config, basename($fullPath, '.php'), Context::STORAGE_ROLE_ROOT);
    }

    /**
     * @param array $config
     * @param null $name
     * @param string $storageRole
     * @return Context
     * @throws \Exception
     */
    public function buildFromArray(array $config, $name = null, $storageRole = Context::STORAGE_ROLE_CHILD)
    {
        $context = new Context($name, null, Context::TYPE_STANDARD, $storageRole);

        if (is_array($config['fields'])) {
            $fields = $config['fields'];

            // Iterate all fields and create a rule set
            foreach ($fields as $fieldName => $fieldData ) {
                $vulnerabilities = $this->buildVulnerabilitySetFromArray($fieldData);
                $vulnElement = new VulnerableElement($vulnerabilities);

                // Add rule to the rule set
                $field = new Field($fieldName, $vulnElement, FieldDescriptor::SOURCE_ANY);
                $context->addField($field);
            }
        }


        foreach (['actions', 'contexts'] as $subContextType) {
            if (is_array($config[$subContextType])) {
                foreach ($config[$subContextType] as $contextName => $contextData) {
                    $child = $this->buildFromArray($contextData, $contextName);
                    $type = $subContextType == 'actions' ? Context::TYPE_ACTION : Context::TYPE_STANDARD;
                    $child->setType($type);
                    $context->addChild($child);
                }
            }
        }

        if (is_array($config['vulnerabilities'])) {
            $vulnerabilities = $this->buildVulnerabilitySetFromArray($config['vulnerabilities']);

            $vulnElement = new VulnerableElement($vulnerabilities);
            $context->setVulnTree($vulnElement);
        }

        return $context;
    }

    /**
     * @param $vulnerabilities
     * @return VulnerabilitySet
     */
    protected function buildVulnerabilitySetFromArray($vulnerabilities)
    {
        $vulnerabilitySet = new VulnerabilitySet();
        if (!is_array($vulnerabilities)) {
            return $vulnerabilitySet;
        }

        $vulnNames = self::getVulnerabilityNames();
        $factory = VulnerabilityFactory::instance();

        // Vulnerabilities are set as array values
        foreach ($vulnNames as $oldName => $newName) {
            if (in_array($oldName, $vulnerabilities)) {
                $vulnerabilitySet->set($factory->create($newName));
            }
        }

        // Vulnerabilities are set as array keys
        foreach ($vulnNames as $oldName => $newName) {
            if (!array_key_exists($oldName, $vulnerabilities)) {
                continue;
            }

            $vuln = $factory->create($newName);

            if (is_array($vulnerabilities[$oldName])) {
                if (array_key_exists('enabled', $vulnerabilities[$oldName])) {
                    $vuln->setEnabled(!!$vulnerabilities[$oldName]['enabled']);
                }

                if ($oldName === 'xss') {
                    if (array_key_exists('stored', $vulnerabilities[$oldName])) {
                        /** @var XSS $vuln */
                        $vuln->setStored($vulnerabilities[$oldName]['stored']);
                    }
                }

                if ($oldName === 'sql') {
                    if (array_key_exists('blind', $vulnerabilities[$oldName])) {
                        /** @var SQL $vuln */
                        $vuln->setBlind($vulnerabilities[$oldName]['blind']);
                    }
                }

            } else {
                $vuln->setEnabled(!!$vulnerabilities[$oldName]);    // It's enabled or not
            }

            $vulnerabilitySet->set($vuln);
        }

        return $vulnerabilitySet;
    }

    public static function getVulnerabilityNames()
    {
        static $names;

        if (!$names) {
            $names = [
                'xss' => XSS::getNameStatic(),
                'sql' => SQL::getNameStatic(),
                'csrf' => CSRF::getNameStatic(),
                'referrer' => Referer::getNameStatic(),
                'os_command' => OSCommand::getNameStatic(),
                'ArbitraryFileUpload' => ArbitraryFileUpload::getNameStatic(),
                'RemoteFileInclude' => RemoteFileInclude::getNameStatic(),
                'XMLExternalEntity' => XMLExternalEntity::getNameStatic()
            ];
        }

        return $names;
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
        return $this->getContextNames($this->basePath);
    }

    /**
     * @return array
     */
    public function getAllContextNames()
    {
        return $this->getContextNames($this->basePath);
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