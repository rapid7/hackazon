<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 18.12.2014
 * Time: 15:15
 */


namespace VulnModule\Form;


use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContextCollectionType extends CollectionType
{
    public function getName()
    {
        return 'context_collection';
    }

    public function getParent()
    {
        return 'collection';
    }

    /**
     * @param OptionsResolverInterface|OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefault('type', 'context');
    }
}