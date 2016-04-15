<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;



class PasswordChangeFormType extends AbstractType
{


    public function getBlockPrefix()
    {
        return 'password_change_form';

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
                    )
                ->add('save', SubmitType::class, array('label' => 'Submit'))
                ->add('Cancel', ButtonType::class, array('attr' => array('data-dismiss' => 'modal')));


    }


    public function configureOptions(OptionsResolver $resolver)
    {

    }


}
