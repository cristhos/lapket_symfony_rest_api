<?php

namespace Masta\PlateFormeBundle\Form\Product;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityRepository;

class ProductType extends AbstractType
{
  private $tokenStorage;

  public function __construct(TokenStorageInterface $tokenStorage)
  {
    $this->tokenStorage = $tokenStorage;
  }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $user = $this->tokenStorage->getToken()->getUser();
          if (!$user) {
              throw new \LogicException('The AlubmFormType cannot be used without an authenticated user!'
          );
      }
        $builder
            ->add('description','textarea')
            ->add('location','text')
            ->add('price','number')
            ->add('album')

        ;

        $builder->addEventListener(
          FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($user) {
                $form = $event->getForm();
                $formOptions = array(
                    'class' => 'Masta\PlateFormeBundle\Entity\Album\Album',
                    'property' => 'name',
                    'query_builder' => function (EntityRepository $er) use ($user) {
                        //Tous les albums dont je suis l'auteur
                    },
                 );
                $form->add('album', 'entity', $formOptions);
            }
          );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Masta\PlateFormeBundle\Entity\Product\Product'
        ));
    }
}
