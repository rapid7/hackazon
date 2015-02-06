<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 23.12.2014
 * Time: 12:03
 */


namespace VulnModule\Form\Condition;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use VulnModule\Form\ConditionType;

class IsAjaxType extends ConditionType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('isAjax', 'checkbox', [
            'label' => false,
            'required' => false
        ]);
    }

    /**
     * @param OptionsResolverInterface|OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'VulnModule\\Config\\Condition\\IsAjax',
        ]);
    }

    public function getName()
    {
        return 'condition_is_ajax';
    }

    public function getParent()
    {
        return 'condition';
    }
}