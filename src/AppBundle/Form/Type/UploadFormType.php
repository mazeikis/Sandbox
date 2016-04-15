<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class UploadFormType extends AbstractType
{


    public function getBlockPrefix()
    {
        return 'image_upload';

    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', FileType::class, array('required' => true, 'data_class' => null))
        ->add('title', TextType::class, array('label' => 'Title of the photo', 'required' => true))
        ->add('description', TextareaType::class, array('label' => 'Description of your photo', 'required' => true))
        ->add('save', SubmitType::class, array('label' => 'Upload'));

    }


    public function configureOptions(OptionsResolver $resolver)
    {

    }


}
