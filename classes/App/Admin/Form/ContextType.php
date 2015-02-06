<?php
/**
 * Created by IntelliJ IDEA.
 * User: Nikolay Chervyakov 
 * Date: 05.02.2015
 * Time: 13:10
 */


namespace App\Admin\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContextType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add($options['context_name'], 'context', [
            'edit_mode_enabled' => $options['edit_mode_enabled']
        ]);

        if ($options['is_config_dir_writable']) {
            $builder->add('Submit', 'submit', [
                'attr' => ['class' => 'btn btn-primary']
            ]);
        }
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'context_form';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'context_name' => 'context',
            'edit_mode_enabled' => false,
            'is_config_dir_writable' => false
        ]);
    }
}