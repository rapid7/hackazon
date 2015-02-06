<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 17.12.2014
 * Time: 14:25
 */


namespace VulnModule\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VulnerableElementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Force possibility to edit vulnerable elements
        $options['edit_mode_enabled'] = true;

        $builder->add('name', $options['edit_mode_enabled'] ? 'text' : 'hidden', [
            'label' => false,
            'attr' => [
                'class' => 'form-control js-collapsible-text-field js-collapsible-field',
                'placeholder' => 'Block name'
            ],
            'required' => false
        ]);

        $builder->add('vulnerabilitySet', 'vulnerability_set', [
            'label' => false,
            'attr' => [
                'class' => 'js-vulnerability-set vulnerability-set'
            ],
        ]);

        $options['vulnRecursionLevel']--;

        if ($options['vulnRecursionLevel'] > 0) {
            $builder->add('children', 'vuln_el_collection', [
                'type' => 'conditional_vulnerable_element',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => false,
                'options' => [
                    'vulnRecursionLevel' => $options['vulnRecursionLevel'],
                    'cascade_validation' => $options['cascade_validation'],
                    'edit_mode_enabled' => $options['edit_mode_enabled'],
                ],
                'by_reference' => false,
                'label' => false,
                'cascade_validation' => $options['cascade_validation'],
                'edit_mode_enabled' => $options['edit_mode_enabled'],
                'attr' => [
                    'class' => 'js-child-vulnerability-elements'
                ]
            ]);
        }
    }

    /**
     * @param OptionsResolverInterface|OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'VulnModule\Config\VulnerableElement',
            'vulnRecursionLevel' => 4,
            'edit_mode_enabled' => false
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'vulnerable_element';
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options);
        $view->vars['edit_mode_enabled'] = $options['edit_mode_enabled'];
    }
}