<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EmailChangeFormType extends AbstractType
{


    public function getName()
    {
        return 'email_change_form';

    }//end getName()


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email', array('label' => 'Your email address: ', 'required' => true, 'constraints' => new NotBlank))
                ->add('save', 'submit', array('label' => 'Register'))
                ->add('Cancel', 'button', array('attr' => array('data-dismiss' => 'modal')));


    }//end buildForm()


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

    }//end setDefaultOptions()


}//end class
