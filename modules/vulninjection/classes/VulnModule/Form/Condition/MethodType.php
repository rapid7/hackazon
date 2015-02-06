<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 23.12.2014
 * Time: 12:03
 */


namespace VulnModule\Form\Condition;


use App\Core\Request;
use App\Helpers\ArraysHelper;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use VulnModule\Form\ConditionType;

class MethodType extends ConditionType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('methods', 'choice', [
            'choices' => ArraysHelper::arrayFillEqualPairs(Request::getMethods()),
            'multiple' => true,
            'expanded' => true,
            'label' => false,
            'required' => true,
//            'constraints' => [
//                new NotBlank(['message' => "At least one method must be selected."]),
//                new Choice([
//                    'message' => "At least one method must be selected.",
//                    'choices' => Request::getMethods(),
//                    'multiple' => true
//                ])
//            ]
        ]);
    }

    /**
     * @param OptionsResolverInterface|OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'VulnModule\\Config\\Condition\\Method',
        ]);
    }

    public function getName()
    {
        return 'condition_method';
    }

    public function getParent()
    {
        return 'condition';
    }
}