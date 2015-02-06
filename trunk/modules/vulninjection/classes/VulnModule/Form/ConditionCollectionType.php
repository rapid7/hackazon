<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 18.12.2014
 * Time: 15:15
 */


namespace VulnModule\Form;


use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use VulnModule\Config\Condition;

class ConditionCollectionType extends CollectionType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $preListener = function (FormEvent $event/*, $eventName, EventDispatcher $dispatcher*/) use ($options) {
            /** @var Form $form */
            $form = $event->getForm();
            $data = $event->getData();

            if (null === $data) {
                $data = array();
            }

            if (!is_array($data) && !($data instanceof \Traversable && $data instanceof \ArrayAccess)) {
                throw new UnexpectedTypeException($data, 'array or (\Traversable and \ArrayAccess)');
            }

            // First remove all rows
            foreach ($form as $name => $child) {
                $form->remove($name);
            }

            // Then add all rows again in the correct order
            foreach ($data as $name => $value) {
                $className = 'VulnModule\\Form\\Condition\\' . $name . 'Type';
                $type = class_exists($className) ? new $className : 'condition';

                $opts = array_replace(array('property_path' => '['.$name.']'), $options['options'] ?: []);
                $opts['cascade_validation'] = $options['cascade_validation'];
                $form->add($name, $type, $opts);
            }
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, $preListener);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, $preListener);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event/*, $eventName, EventDispatcher $dispatcher*/) use ($options) {
//            /** @var Form $form */
//            $form = $event->getForm();
            $data = $event->getData();

            if (null === $data) {
                return;
            }

            if (!is_array($data) && !($data instanceof \Traversable && $data instanceof \ArrayAccess)) {
                throw new UnexpectedTypeException($data, 'array or (\Traversable and \ArrayAccess)');
            }

            /**
             * @var string $name
             * @var Condition $value
             */
            foreach ($data as $name => $value) {
                if ($value->getName() == $name) {
                    continue;
                }
                $className = 'VulnModule\\Config\\Condition\\' . $name;
                /** @var Condition $element */
                $element = new $className;
                $element->fillFromArray($value->toArray());
                $data[$name] = $element;

            }

            $event->setData($data);
        });
    }

    public function getName()
    {
        return 'condition_collection';
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
        $resolver->setDefault('type', 'condition');
    }
}