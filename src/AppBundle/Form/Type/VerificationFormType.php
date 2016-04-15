<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class VerificationFormType extends AbstractType
{


    public function getBlockPrefix()
    {
        return 'verification_form';

    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'plainPassword',
            'repeated',
            array(
             'first_name'  => 'password',
             'second_name' => 'confirm',
             'type'        => 'password',
             'constraints' => new Length(array('min' => 6, 'max' => 4096), new NotBlank),
            )
        )->add('save', 'submit', array('label' => 'Submit'));

    }


    public function configureOptions(OptionsResolver $resolver)
    {

    }


}
