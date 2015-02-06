<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 13.01.2015
 * Time: 15:53
 */


namespace App\Admin\Form;


use App\Core\Request;
use App\Helpers\ArraysHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Choice;

class MatrixFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('method', 'choice', [
            'choices' => ArraysHelper::arrayFillEqualPairs(Request::getMethods()),
            'label' => 'Method',
            'required' => false,
            'empty_data' => '',
            'multiple' => false,
            'constraints' => [
                new Choice([
                    'message' => "Method must be selected.",
                    'choices' => array_merge([''], Request::getMethods()),
                    'multiple' => false
                ])
            ],
            'attr' => [
                'class' => 'form-control'
            ]
        ]);

        $isAjaxChoices = ['no', 'yes'];

        $builder->add('is_ajax', 'choice', [
            'label' => 'Is Ajax',
            'required' => true,
            'empty_data' => '',
            'choices' => ArraysHelper::arrayFillEqualPairs($isAjaxChoices),
            'constraints' => [
                new Choice([
                    'message' => "Incorrect value selected",
                    'choices' => $isAjaxChoices,
                    'multiple' => false
                ])
            ],
            'attr' => [
                'class' => 'form-control'
            ]
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'matrix_filter';
    }
}