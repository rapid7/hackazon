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
use VulnModule\VulnerabilityFactory;

/**
 * Renders the context in HTML format
 * @package VulnModule\Storage
 */
class HTMLWriter implements IWriter
{
    protected $pixie;

    protected $viewPath = '';

    function __construct(Pixie $pixie)
    {
        $this->pixie = $pixie;
        $this->viewPath = __DIR__.'/../../../../../assets/views/admin/context';
    }

    public function write(Context $context)
    {
        $view = $this->pixie->view('admin/context/index');
        $view->result = $this->renderContext($context);
        return $view->render();
    }

    public function renderContext(Context $context)
    {
        $children = '';
        $fields = '';

        $vulnerabilities = $this->renderVulnerabilityTree($context->getVulnerabilityElement());

        if ($context->hasFields()) {
            $fieldsHtml = [];
            foreach ($context->getFields() as $field) {
                $fieldsHtml[] = $this->renderField($field);
            }
            $fields = implode('', $fieldsHtml);
        }

        if ($context->hasChildren()) {
            $childrenHtml = [];

            foreach ($context->getChildrenArray() as $child) {
                $childrenHtml[] = $this->renderContext($child);
            }

            $children = implode('', $childrenHtml);
        }


        $view = $this->pixie->view('admin/context/context');
        $view->vulnerabilities = $vulnerabilities;
        $view->children = $children;
        $view->fields = $fields;
        $view->contextName = $context->getName();
        $view->type = $context->getType();
        return $view->render();
    }

    /**
     * @param VulnerableElement $element
     * @return string
     */
    public function renderVulnerabilityTree(VulnerableElement $element)
    {
        $vulnerabilities = [];
        $childrenVulns = '';
        $conditions = [];

        if ($element->hasChildren()) {
            $childrenHtml = [];

            foreach ($element->getChildrenArray() as $child) {
                $childrenHtml[] = $this->renderVulnerabilityTree($child);
            }

            $childrenVulns = implode('', $childrenHtml);
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
        }
        sort($vulnerabilities);

        $vulnNames = VulnerabilityFactory::instance()->getAllVulnerabilityNames();
        $computedVulnerabilities = [];

        /** @var Vulnerability $vuln */
        foreach ($vulnNames as $vulnName) {
            $computedVulnerabilities[] = $element->getComputedVulnerability($vulnName)->asArray();
        }

        $view = $this->pixie->view('admin/context/vuln_element');
        $view->vulnerabilities = $vulnerabilities;
        $view->computedVulnerabilities = $computedVulnerabilities;
        $view->childrenVulns = $childrenVulns;
        $view->conditionList = $conditions;

        return $view->render();
    }

    private function renderField(Field $field)
    {
        $view = $this->pixie->view('admin/context/field');
        $view->fieldName = $field->getName();
        $view->source = $field->getSource();
        $view->vulnerabilities = $this->renderVulnerabilityTree($field->getVulnerabilityElement());

        return $view->render();
    }
}