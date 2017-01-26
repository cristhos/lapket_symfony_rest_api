<?php

namespace Masta\PlateFormeBundle\Form\Album;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityRepository;


class AlbumType extends AbstractType
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
            ->add('name','text')
            ->add('description','textarea')
        ;

        $builder->addEventListener(
          FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($user) {
                $form = $event->getForm();
                $formOptions = array(
                    'class' => 'Masta\PlateFormeBundle\Entity\Category\Category',
                    'property' => 'id',
                    'query_builder' => function (EntityRepository $er) use ($user) {
                        //Toute les category que je suis
                    },
                 );
                $form->add('category', 'entity', $formOptions);
            }
          );
    }



    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Masta\PlateFormeBundle\Entity\Album\Album',
        ));
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'masta_plateformebundle_album';
    }
}
