<?php
namespace Masta\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Masta\UserBundle\Form\Type\GenderType;
use Masta\UserBundle\Form\Type\LanguageType;
use Masta\PlateFormeBundle\Form\Picture\PictureType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('profilePicture', PictureType::class)
                ->add('firstName','text',array('required' =>false))
                ->add('lastName','text',array('required' =>false))
                ->add('gender',GenderType::class)
                ->add('birthday','birthday',array('required' =>false))
                ->add('language',LanguageType::class)
                ->add('description','textarea',array('required' =>false))
                ->add('country','country',array('required' =>false))
                ->add('city','text',array('required' =>false))
                ->add('webSite','url',array('required' =>false))
                ->add('notifications','checkbox',array('required' =>false))
                ->add('cookies','checkbox',array('required' =>false))
                ->add('isPrivate','checkbox',array('required' =>false));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function getBlockPrefix()
    {
        return 'masta_user_profile';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
