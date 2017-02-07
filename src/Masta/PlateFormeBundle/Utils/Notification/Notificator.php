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

  /**
   * @var TokenStorageInterface
   */
  private $tokenStorage;

  /**
   * @var Swift_Mailer
   */
  private $mailer;


  // injecte in EntityManager, TokenStorage and Swift_Mailler
  public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, \Swift_Mailer $mailer)
  {
    $this->em = $em;
    $this->tokenStorage = $tokenStorage;
    $this->mailer = $mailer;
  }
  
  //notify in switch option
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
  
  //add follower notification in database
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

  //add ProductVote notifcation in database(notifify auteur and participants)
  public function persistNotifyProduct(Product $object)
  {
    $user = $this->tokenStorage->getToken()->getUser();

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

  //update seen notify and update user status
  public function seen($notifications)
  {

    foreach($notifications as $notification) {
        if($notification->getIsSeen() == false)
        {
            $notification->setIsSeen(true);
            $this->em->persist($notification);
            $this->em->flush();

            $user = $this->tokenStorage->getToken()->getUser();
            $compteur = $user->getNbNotifications();

            if($compteur>0)
               $user->setNbNotifications($compteur-1);
            else
               $user->setNbNotifications(0);
    
            $this->em->persist($user);
            $this->em->flush();
        }
    }   
  }
}
