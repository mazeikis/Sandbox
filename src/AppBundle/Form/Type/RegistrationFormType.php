<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class RegistrationFormType extends AbstractType
{


    public function getBlockPrefix()
    {
        return 'user_registration';

    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class, array('label' => 'Choose username: ', 'required' => true))
        ->add(
            'plainPassword',
            RepeatedType::class,
            array(
             'first_name'  => 'password',
             'second_name' => 'confirm',
             'type'        => PasswordType::class,
            )
        )
        ->add('firstName', TextType::class, array('label' => 'Your first name: ', 'required' => true))
        ->add('lastName', TextType::class, array('label' => 'Your last name: ', 'required' => true))
        ->add('email', EmailType::class, array('label' => 'Your email address: ', 'required' => true))
        ->add('save', SubmitType::class, array('label' => 'Register'));

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'AppBundle\Entity\User' ));

    }


}
