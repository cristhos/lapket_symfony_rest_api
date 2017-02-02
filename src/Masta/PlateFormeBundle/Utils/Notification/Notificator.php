<?php
namespace Masta\PlateFormeBundle\Utils\Notification;

use Doctrine\ORM\EntityManagerInterface;
use Masta\UserBundle\Entity\Follower;
use Masta\UserBundle\Entity\User;
use Masta\PlateFormeBundle\Entity\Product\Product;
use Masta\PlateFormeBundle\Entity\Notification\Notification;
use Masta\PlateFormeBundle\Entity\Product\ProductVote;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class Notificator 
{
  /**
   * @var EntityManagerInterface
   */
  private $em;

  private $tokenStorage;

  private $mailer;


  // On injecte l'EntityManager
  public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, \Swift_Mailer $mailer)
  {
    $this->em = $em;
    $this->tokenStorage = $tokenStorage;
    $this->mailer = $mailer;
  }

  public function nofify($object)
  {
      switch ($object) {
        case $object instanceOf Follower:
            $this->persistNotifyFollower($object);
          break;
       case $object instanceOf Product:
             $this->persistNotifyProduct($object);
          break;
        default:
            # code...
          break;
      }
  }

  public function persistNotifyFollower(Follower $object)
  {
    $user = $this->tokenStorage->getToken()->getUser();
    //notify le destinateur sauf qui ne peut pas etre l'auteur actuel
    if($object->getUserFollowed() != $user){
      $notification = new Notification();
      $notification->setObjectId($object->getId());
      $notification->setAuthor($user);
      $notification->setType('Follower');
      $notification->setDestinator($object->getUserFollowed());
      $this->em->persist($notification);
      $this->em->flush();

    }
  }
  public function persistNotifyProduct(Product $object){
    $user = $this->tokenStorage->getToken()->getUser();

    //nofify l'author du produit des votes des autres
    if($user != $object->getAuthor())
    {
      $notification = new Notification();
      $notification->setObjectId($object->getId());
      $notification->setType('ProductVote');
      $notification->setAuthor($user);
      $notification->setDestinator($object->getAuthor());
      $this->em->persist($notification);
      $this->em->flush();
    }
    //notify les autres
      foreach ($object->getVotes() as $vote) 
      {
        if($vote->getIsNotify())
        {
          $notification = new Notification();
          $notification->setObjectId($object->getId());
          $notification->setType('ProductVote');
          $notification->setAuthor($user);
          $notification->setDestinator($vote->getAuthor());
          $this->em->persist($notification);
        }
        $this->em->flush();
      }
      
  }

  public function seen($notifications)
  {

    foreach($notifications as $notification) {
        if($notification->getIsSeen() == false)
        {
            $notification->setIsSeen(true);
            $this->em->persist($notification);
            $this->em->flush();

            $destinator = $notification->getDestinator();
            $compteur = $destinator->getNotifications()->count();
            $destinator->setNbNotifications($compteur - 1);
            $this->em->persist($destinator);
            $this->em->flush();


        }
    }   
  }
}
