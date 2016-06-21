<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class LoginFormType extends AbstractType
{


    public function getBlockPrefix()
    {
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('_username', TextType::class, array('label' => 'Username: ', 'required' => true))
        ->add(
            '_password', 
            PasswordType::class, 
            array('label'  => 'Password: ', 
                  'required' => true, 
                  'constraints' => new Length(array('min' => 6, 'max' => 4096), new NotBlank)
                )
            )
        ->add('save', SubmitType::class, array('label' => 'Login'));
    }


    public function configureOptions(OptionsResolver $resolver)
    {

    }


}
