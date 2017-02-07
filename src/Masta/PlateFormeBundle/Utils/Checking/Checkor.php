<?php
namespace Masta\PlateFormeBundle\Utils\Checking;


use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Masta\UserBundle\Entity\User;

class Checkor
{
  /**
   * @var TokenStorageInterface
   */
  private $tokenStorage;

  // On injecte TokenStorageInterface
  public function __construct(TokenStorageInterface $tokenStorage)
  {
    $this->tokenStorage = $tokenStorage;
  }

////FONCTIONS QUI CONCERNE LES TRAITEMENTS DES PRODUIS

  public function checkProducts($products)
  {
    foreach ($products as $product )
    {
      $this->checkProduct($product);
    }
  }
  public function checkProduct($product){
    $user = $this->tokenStorage->getToken()->getUser();
    if($product->getAuthor()->getProfilePicture() !== null)
    {
      $this->checkUser($product->getAuthor());
    }
    if($product->getAuthor() == $user)
    {
      $product->setIsAuthor(true);
    }
    else
    {
      $product->setIsAuthor(false);
    }

     if($product->getPicture() != null) $this->checkPicture($product->getPicture());
  
      $product->setIsVoted(false);
      foreach ($product->getVotes() as $vote)
      {
          if($vote->getAuthor() == $user)
          {
            $product->setIsVoted(true);
            $product->setIsNotify($vote->getIsNotify());
            break;
          } 
      }


  }

  ///CONCERNE L'UTILISATEUR
  public function checkUsers($users)
  {
    foreach ($users as $user)
    {
      $this->checkUser($user);
    }
  }

  public function checkUser($user)
  {
    //Verification user == actual_user
    $actual_user = $this->tokenStorage->getToken()->getUser();

     //verification avec isInstanceOf: urgent

    if($actual_user == $user){
      $user->setIsAuthor(true);
    }else{
      $user->setIsAuthor(false);
    }

    if($user->getProfilePicture() !== null)
    {
      $this->checkPicture($user->getProfilePicture());
    }

    //VERIFICATION DE L'ETAS DE L'INSCRIPTION
    $user->setCompteState(false);
    if(($user->getFullName() != null) && ($user->getCountry() != null) &&
    ($user->getCity() != null))
    {
      $user->setCompteState(true);
    }

    $user->setIsFollowIt(false);
    $user->setIsFollowMe(false);

    if($actual_user instanceOf User){
          if($user != $actual_user)
            {
                    foreach ($actual_user->getFollowers() as $follower)
                    {
                      if($follower->getUserFollowed() == $user) $user->setIsFollowIt(true);
                    }

                    foreach ($actual_user->getFollows() as $follows)
                    {
                      if($follows->getAuthor() == $user) $user->setIsFollowMe(true);
                    }
                }
    }
  }


  //Follower
  public function checkFollowers($followers)
  {
    foreach ($followers as $f)
    {
      $this->checkUser($f->getAuthor());
    }
  }

  //Follows
  public function checkFollows($follows)
  {
    foreach ($follows as $f)
    {
      $this->checkUser($f->getUserFollowed());
    }
  }    

  //Notifications
  public function checkNotifications($notifications)
  {
    foreach ($notifications as $n)
    {
      $this->checkUser($n->getAuthor());
      $this->checkUser($n->getDestinator());
    }
  }


  //Albums
  public function checkAlbums($albums)
  {
    foreach ($albums as $album )
    {
      $this->checkAlbum($album);
    }
  }

  public function checkAlbum($album)
  {
    $user = $this->tokenStorage->getToken()->getUser();
    if($album->getAuthor()->getProfilePicture() !== null)
    {
      $this->checkUser($album->getAuthor());
    }

    if($album->getAuthor() == $user)
    {
      $album->setIsAuthor(true);
    }
    else
    {
      $album->setIsAuthor(false);
    }
  }


  ////FONCTIONS QUI CONCERNE LES TRAITEMENTS DES CONVERSATIONS
    public function checkConversations($conversations)
    {
      foreach ($conversations as $conversation )
      {
        $this->checkConversation($conversation);
      }
    }

    public function checkConversation($conversation)
    {
      $this->checkUser($conversation->getAuthor());
      $this->checkProduct($conversation->getProduct());
    }


    ////FONCTIONS QUI CONCERNE LES TRAITEMENTS DES MESSAGES
    public function checkMessages($messages)
    {
      foreach ($messages as $message )
      {
        $this->checkMessage($message);
      }
    }

    public function checkMessage($message)
    {
      $this->checkUser($message->getAuthor());
    }


    ///FONCTIONS QUI AJOUTE UN URL AU IMAGES

    public function checkPictures($pictures)
    {
      foreach($picture as $pictures) 
      {
        $this->checkPicture($picture);
      }
    }

    public function checkPicture($picture)
    {
      $picture->setWebPath('http://localhost/masta/web/'.$picture->getWebPath());
      //$picture->setWebPath('http://192.168.173.78/masta/web/'.$picture->getWebPath());
    }

}
