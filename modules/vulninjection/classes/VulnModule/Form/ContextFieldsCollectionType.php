<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 18.12.2014
 * Time: 15:15
 */


namespace VulnModule\Form;


use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContextFieldsCollectionType extends CollectionType
{
    public function getName()
    {
        return 'context_fields_collection';
    }

    public function getParent()
    {
        return 'collection';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults([
            'edit_mode_enabled' => false,
        ]);
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options);
        $view->vars['edit_mode_enabled'] = $options['edit_mode_enabled'];
    }
}