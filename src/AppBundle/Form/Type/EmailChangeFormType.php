<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;


class EmailChangeFormType extends AbstractType
{


    public function getBlockPrefix()
    {
        return 'email_change_form';

    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', EmailType::class, array('label' => 'Your email address: ', 'required' => true, 'constraints' => new NotBlank))
                ->add('save', SubmitType::class, array('label' => 'Register'))
                ->add('Cancel', ButtonType::class, array('attr' => array('data-dismiss' => 'modal')));


    }


    public function configureOptions(OptionsResolver $resolver)
    {

    }


}
