<?php
namespace AppBundle\Type\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VerificationFormType extends AbstractType
{
	public function getName()
	{
		return 'verification_form';
	}
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('username', 'text', array('label'  => 'Your username: ', 'required' => true))
		    ->add('reset password', 'submit');
	}
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
	}
}
