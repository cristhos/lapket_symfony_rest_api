<?php
namespace Masta\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class LanguageType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array(
            'fr' => 'Francais',
            'en' => 'Anglais',
            )
        ));
    }
    public function getParent()
    {
        return ChoiceType::class;
    }
}