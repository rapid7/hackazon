<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 17.12.2014
 * Time: 14:25
 */


namespace VulnModule\Form;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConditionalVulnerableElementType extends VulnerableElementType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('conditionSet', 'condition_set', [
            'property_path' => 'conditions',
            'label' => 'Conditions:',
            'cascade_validation' => $options['cascade_validation'],
            'attr' => [
                'class' => 'condition-set js-condition-set'
            ]
        ]);

        parent::buildForm($builder, $options);
    }

    /**
     * @param OptionsResolverInterface|OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'VulnModule\Config\ConditionalVulnerableElement',
            'vulnRecursionLevel' => 3
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'conditional_vulnerable_element';
    }

    public function getParent()
    {
        return 'vulnerable_element';
    }
}