<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{


    public function getName()
    {
        return 'user_registration';

    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text', array('label' => 'Choose username: ', 'required' => true))
        ->add(
            'plainPassword',
            'repeated',
            array(
             'first_name'  => 'password',
             'second_name' => 'confirm',
             'type'        => 'password',
            )
        )
        ->add('firstName', 'text', array('label' => 'Your first name: ', 'required' => true))
        ->add('lastName', 'text', array('label' => 'Your last name: ', 'required' => true))
        ->add('email', 'email', array('label' => 'Your email address: ', 'required' => true))
        ->add('save', 'submit', array('label' => 'Register'));

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => 'AppBundle\Entity\User' ));

    }


}
