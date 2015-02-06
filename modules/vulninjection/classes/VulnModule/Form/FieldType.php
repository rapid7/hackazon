<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 17.12.2014
 * Time: 14:23
 */


namespace VulnModule\Form;


use App\Helpers\ArraysHelper;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use VulnModule\Config\FieldDescriptor;

class FieldType extends VulnerabilityHostType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $sourceOptions = [
            'attr' => ['class' => 'form-control input-miniature field-source js-field-source'],
            'constraints' => [
                new Choice([
                    'message' => "At least one method must be selected.",
                    'choices' => FieldDescriptor::getSources(),
                    'multiple' => false
                ]),
                new NotBlank()
            ]
        ];
        if ($options['edit_mode_enabled']) {
            $sourceOptions['choices'] = ArraysHelper::arrayFillEqualPairs(FieldDescriptor::getSources());
            $sourceOptions['multiple'] = false;

        } else {
            $sourceOptions['label'] = false;
        }

        $builder->add('source', $options['edit_mode_enabled'] ? 'choice' : 'hidden', $sourceOptions);

        $vulnTree = $builder->get('vulnTree');
        $builder->remove('vulnTree');
        $builder->add($vulnTree);
    }

    /**
     * @param OptionsResolverInterface|OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'VulnModule\Config\Field',
            'error_bubbling' => false,
        ]);
    }

    public function getName()
    {
        return 'field';
    }

    public function getParent()
    {
        return 'vulnerability_host';
    }
}