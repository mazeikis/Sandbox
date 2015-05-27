<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactFormType extends AbstractType
{


    public function getName()
    {
        return 'contact_form';

    }//end getName()


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder ->add('from', 'email', array('label' => 'From', 'required' => true))
        ->add('subject', 'text', array('label' => 'Subject', 'required' => true))
        ->add('message', 'textarea', array('label' => 'Message:', 'required' => true))
        ->add('Send', 'submit');

    }//end buildForm()


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

    }//end setDefaultOptions()


}//end class
