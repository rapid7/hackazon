<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov
 * Date: 08.12.2014
 * Time: 16:46
 */


namespace VulnModule\Storage;


use App\Pixie;
use VulnModule\Config\ConditionalVulnerableElement;
use VulnModule\Config\Context;
use VulnModule\Config\Field;
use VulnModule\Config\ICondition;
use VulnModule\Config\VulnerableElement;
use VulnModule\Vulnerability;


/**
 * Renders the context in HTML format
 * @package VulnModule\Storage
 */
class PHPFileWriter implements IWriter
{
    protected $pixie;

    protected $targetPath;

    function __construct(Pixie $pixie, $targetPath)
    {
        $this->pixie = $pixie;
        $this->targetPath = $targetPath;
    }

    public function write(Context $context)
    {
        $result = $this->asArray($context);
        $path = $this->targetPath . '/' . $context->getName() . '.php';
        $content = "<?php\nreturn " . var_export($result, true) . ";";
        $content = preg_replace("/^(\\s+)/m", "$1$1", $content);
        file_put_contents($path, $content);
    }

    public function asArray(Context $context)
    {
        $children = [];
        $fields = [];

        $vulnerabilities = $this->vulnerabilityTreeAsArray($context->getVulnerabilityElement());

        if ($context->hasFields()) {
            $fieldsArr = [];
            foreach ($context->getFields() as $field) {
                $fieldsArr[] = $this->renderField($field);
            }
            $fields = $fieldsArr;
        }

        if ($context->hasChildren()) {
            $childrenArr = [];

            foreach ($context->getChildrenArray() as $child) {
                $childrenArr[$child->getName()] = $this->asArray($child);
            }

            $children = $childrenArr;
        }

        $result = [
            'name' => $context->getName(),
            'type' => $context->getType(),
            'technology' => $context->getTechnology(),
        ];
        if ($context->getMappedTo()) {
            $result['mapped_to'] = $context->getMappedTo();
        }

        if ($context->getStorageRole() != Context::STORAGE_ROLE_CHILD) {
            $result['storage_role'] = $context->getStorageRole();
        }

        if (count($fields)) {
            $result['fields'] = $fields;
        }

        if (count($vulnerabilities)) {
            $result['vulnerabilities'] = $vulnerabilities;
        }

        if (count($children)) {
            $result['children'] = $children;
        }

        return $result;
    }

    /**
     * @param VulnerableElement $element
     * @return string
     */
    public function vulnerabilityTreeAsArray(VulnerableElement $element)
    {
        $result = [];

        $vulnerabilities = [];
        $children = [];
        $conditions = [];

        if ($element->hasChildren()) {
            $childrenArr = [];

            foreach ($element->getChildrenArray() as $child) {
                $childrenArr[] = $this->vulnerabilityTreeAsArray($child);
            }

            $children = $childrenArr;
        }

        if ($element instanceof ConditionalVulnerableElement) {
            /** @var ICondition $condition */
            foreach ($element->getConditions()->getConditions() as $condition) {
                $conditions[$condition->getName()] = $condition->toArray();
            }
        }

        /** @var Vulnerability $vuln */
        foreach ($element->getVulnerabilitySet()->getVulnerabilities() as $vuln) {
            $vulnerabilities[$vuln->getName()] = $vuln->asArray();
            unset($vulnerabilities[$vuln->getName()]['name']);
        }

        ksort($vulnerabilities);

        if ($element->getName()) {
            $result['name'] = $element->getName();
        }

        if (count($conditions)) {
            $result['conditions'] = $conditions;
        }

        if (count($vulnerabilities)) {
            $result['vuln_list'] = $vulnerabilities;
        }

        if (count($children)) {
            $result['children'] = $children;
        }

        return $result;
    }

    private function renderField(Field $field)
    {
        $result = [
            'name' => $field->getName(),
            'source' => $field->getSource()
        ];

        $vulnerabilities = $this->vulnerabilityTreeAsArray($field->getVulnerabilityElement());

        if (count($vulnerabilities)) {
            $result['vulnerabilities'] = $vulnerabilities;
        }

        return $result;
    }
}