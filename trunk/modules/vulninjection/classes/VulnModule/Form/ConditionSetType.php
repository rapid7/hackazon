<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 19.12.2014
 * Time: 10:45
 */


namespace VulnModule\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConditionSetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('conditions', 'condition_collection', [
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => false,
            'by_reference' => false,
            'label' => false,
            'cascade_validation' => $options['cascade_validation'],
            'attr' => [
                'class' => 'js-condition-list condition-list'
            ],
        ]);
    }

    /**
     * @param OptionsResolverInterface|OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'VulnModule\\Config\\ConditionSet',
        ]);
    }

    public function getName()
    {
        return 'condition_set';
    }
}