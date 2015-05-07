<?php
namespace AppBundle\Type\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UploadFormType extends AbstractType
{
	public function getName()
	{
		return 'image_upload';
	}
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('title','text',array('label'  => 'Title of the photo', 'required' => true))
             ->add('file', 'file',array('required' => true, 'data_class' => null))
             ->add('description','textarea',array('label' => 'Description of your photo', 'required' => true))
             ->add('save', 'submit', array('label' => 'Upload'));
	}
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	}
}
