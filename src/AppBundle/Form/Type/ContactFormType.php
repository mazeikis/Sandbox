<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;




class ContactFormType extends AbstractType
{


    public function getBlockPrefix()
    {
        return 'contact_form';

    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder ->add('from', EmailType::class, array('label' => 'From', 'required' => true))
        ->add('subject', TextType::class, array('label' => 'Subject', 'required' => true))
        ->add('message', TextareaType::class, array('label' => 'Message:', 'required' => true, 'attr' => array('class' => 'contact-form-textarea')))
        ->add('Send', SubmitType::class);

    }


    public function configureOptions(OptionsResolver $resolver)
    {

    }


}
