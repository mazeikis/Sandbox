<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordChangeFormType extends AbstractType
{


    public function getName()
    {
        return 'password_change_form';

    }//end getName()


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
                    'plainPassword',
                    'repeated',
                    array(
                         'first_name'  => 'password',
                         'second_name' => 'confirm',
                         'type'        => 'password',
                         'constraints' => new Length(array('min' => 6, 'max' => 4096), new NotBlank),
                        )
                    )
                ->add('save', 'submit', array('label' => 'Submit'))
                ->add('Cancel', 'button', array('attr' => array('data-dismiss' => 'modal')));


    }//end buildForm()


    public function configureOptions(OptionsResolver $resolver)
    {

    }//end configureOptions()


}//end class
