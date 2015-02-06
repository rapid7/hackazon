<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 17.12.2014
 * Time: 17:35
 */


namespace VulnModule\Form\Extension;


use Symfony\Component\Form\AbstractExtension;
use VulnModule\Form as Form;
use VulnModule\Form\Vulnerability as V;
use VulnModule\Form\Condition as C;

/**
 * Vulnerability-related field types.
 * @package VulnModule\Form\Extension
 */
class VulnExtension extends AbstractExtension
{
    protected function loadTypes()
    {
        return [
            new Form\VulnerabilityHostType(),
            new Form\ContextType(),
            new Form\FieldType(),
            new Form\ContextFieldsCollectionType(),
            new Form\VulnerableElementType(),
            new Form\VulnerableElementCollectionType(),
            new Form\VulnerabilitySetType(),
            new Form\VulnerabilityType(),
            new Form\VulnerabilityCollectionType(),
            new Form\ConditionalVulnerableElementType(),
            new Form\ContextCollectionType(),
            new Form\ConditionType(),
            new Form\ConditionSetType(),
            new Form\ConditionCollectionType(),

            // Vuln types:
            new V\XSSType(),
            new V\SQLType(),
            new V\IntegerOverflowType(),
            new V\PHPSessionIdOverflowType(),

            // Condition types:
            new C\MethodType(),
            new C\IsAjaxType()
        ];
    }
}