<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 17.12.2014
 * Time: 14:22
 */


namespace VulnModule\Form;


use App\Helpers\ArraysHelper;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ExecutionContext;
use VulnModule\Config\Context;


class ContextType extends VulnerabilityHostType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $typeOptions = [
            'attr' => ['class' => 'form-control input-miniature'],
            'error_bubbling' => true,
            'constraints' => [
                new Choice([
                    'message' => "Incorrect context type selected.",
                    'choices' => Context::getTypes(),
                    'multiple' => false
                ]),
                new NotBlank(['message' => 'Context type is missing.'])
            ]
        ];
        if ($options['edit_mode_enabled']) {
            $typeOptions['choices'] = ArraysHelper::arrayFillEqualPairs(Context::getTypes());
            $typeOptions['multiple'] = false;
        }
        $builder->add('type', $options['edit_mode_enabled'] ? 'choice' : 'hidden', $typeOptions);

        $showTech = false; //$options['edit_mode_enabled'];

        $techOptions = [
            'attr' => ['class' => 'form-control input-miniature'],
            'error_bubbling' => true,
            'constraints' => [
                new Choice([
                    'message' => "Incorrect technology selected.",
                    'choices' => Context::getTechnologies(),
                    'multiple' => false
                ]),
                new NotBlank(['message' => 'Context technology missing.'])
            ]
        ];
        if ($showTech) {
            $techOptions['choices'] = Context::getTechnologiesLabels();
            $techOptions['multiple'] = false;
        }
        $builder->add('technology', $showTech ? 'choice' : 'hidden', $techOptions);

        $builder->add('fields', 'context_fields_collection', [
            'type' => 'field',
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => false,
            'by_reference' => false,
            'options' => [
                'label' => false,
                'edit_mode_enabled' => $options['edit_mode_enabled'],
                'cascade_validation' => $options['cascade_validation'],
            ],
            'cascade_validation' => $options['cascade_validation'],
            'error_bubbling' => false,
            'edit_mode_enabled' => $options['edit_mode_enabled'],
            'attr' => [
                'class' => 'js-fields-container'
            ]
        ]);

        $vulnTree = $builder->get('vulnTree');
        $builder->remove('vulnTree');
        $builder->add($vulnTree);

        $options['recursionLevel']--;

        if ($options['recursionLevel'] > 0) {
            $builder->add('children', 'context_collection', [
                'type' => 'context',
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => false,
                'options' => [
                    'recursionLevel' => $options['recursionLevel'],
                    'label' => false,
                    'edit_mode_enabled' => $options['edit_mode_enabled'],
                    'cascade_validation' => $options['cascade_validation'],
                ],
                'by_reference' => false,
                'label' => false,
                'cascade_validation' => $options['cascade_validation'],
                'error_bubbling' => false,
                'attr' => [
                    'class' => 'js-child-contexts'
                ]
            ]);
        }

        $builder->add('mappedTo', 'hidden');
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'context';
    }

    /**
     * @param OptionsResolverInterface|OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'VulnModule\Config\Context',
            'recursionLevel' => 4,
            'cascade_validation' => true,
            'constraints' => [
                new Callback([$this, 'checkContextHierarchy'])
            ]
        ]);
    }

    public function getParent()
    {
        return 'vulnerability_host';
    }

    /**
     * @param Context $context
     * @param ExecutionContext $execContext
     */
    public static function checkContextHierarchy($context, ExecutionContext $execContext)
    {
        ///** @var Context $contextData */
        //$contextData = $context->getValue();
//        var_dump($context->getType());
//        var_dump($execContext->getPropertyPath());
    }
}