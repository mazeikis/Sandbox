<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
            RepeatedType::class,
            array(
             'first_name'  => 'password',
             'second_name' => 'confirm',
             'type'        => PasswordType::class,
             'constraints' => new Length(array('min' => 6, 'max' => 4096), new NotBlank),
            )
        )->add('save', SubmitType::class, array('label' => 'Submit'));

    }


    public function configureOptions(OptionsResolver $resolver)
    {

    }


}
