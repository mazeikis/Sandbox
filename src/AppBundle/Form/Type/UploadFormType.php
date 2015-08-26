<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UploadFormType extends AbstractType
{


    public function getName()
    {
        return 'image_upload';

    }//end getName()


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'file', array('required' => true, 'data_class' => null))
        ->add('title', 'text', array('label' => 'Title of the photo', 'required' => true))
        ->add('description', 'textarea', array('label' => 'Description of your photo', 'required' => true))
        ->add('save', 'submit', array('label' => 'Upload'));

    }//end buildForm()


    public function configureOptions(OptionsResolver $resolver)
    {

    }//end configureOptions()


}//end class
