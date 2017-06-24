<?php
namespace Masta\PlateFormeBundle\Utils\Checking;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Masta\UserBundle\Entity\User;

class Checkor
{
  /**
   * @var TokenStorageInterface
   */
  private $tokenStorage;

  /**
   * @var ContainerInterface
   */
   private $container;

  //injecte TokenStorageInterface and ContainerInterface
  public function __construct(TokenStorageInterface $tokenStorage, ContainerInterface $container)
  {
    $this->tokenStorage = $tokenStorage;
    $this->container = $container;
  }

  // each array products and check specific
  public function checkProducts($products)
  {
    foreach ($products as $product ) $this->checkProduct($product);
  }

  // check a product
  public function checkProduct($product)
  {
    $user = $this->tokenStorage->getToken()->getUser();

    if($product->getAuthor()->getProfilePicture() !== null) $this->checkUser($product->getAuthor());

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
    foreach ($users as $user) $this->checkUser($user);
  }

  public function checkUser($user)
  {
    //Verification user == actual_user
    $actual_user = $this->tokenStorage->getToken()->getUser();

     //verification avec isInstanceOf: urgent

    if($actual_user == $user){
      $user->setIsAuthor(true);
    }
    else
    {
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

    if($actual_user instanceOf User)
    {
          if($user != $actual_user)
            {
                    foreach ($actual_user->getFollows() as $follower)
                    {
                      if($follower->getUserFollowed() == $user) $user->setIsFollowIt(true);
                    }

                    foreach ($actual_user->getFollowers() as $follows)
                    {
                      if($follows->getAuthor() == $user) $user->setIsFollowMe(true);
                    }
           }
    }
  }


  //Follower
  public function checkFollowers($followers)
  {
    foreach ($followers as $f)  $this->checkUser($f->getAuthor());
  }

  //Follows
  public function checkFollows($follows)
  {
    foreach ($follows as $f)  $this->checkUser($f->getUserFollowed());
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
    foreach ($albums as $album )  $this->checkAlbum($album);
  }

  public function checkAlbum($album)
  {
    $user = $this->tokenStorage->getToken()->getUser();
    if($album->getAuthor()->getProfilePicture() !== null) $this->checkUser($album->getAuthor());
    

    if($album->getAuthor() == $user)
    {
      $album->setIsAuthor(true);
    }
    else
    {
      $album->setIsAuthor(false);
    }
  }

    // each array() and check specifique picture
    public function checkPictures($pictures)
    {
      foreach($picture as $pictures)  $this->checkPicture($picture);
    }
    //add url to one picture
    public function checkPicture($picture)
    {
      $kernel = $this->container->get('kernel');
      if($kernel->getEnvironment() == "prod")
      {
        $picture->setWebPath('https://apis.lapket.com/'.$picture->getWebPath());
      }
      else if($kernel->getEnvironment() == "pre_prod")
      {
         //$picture->setWebPath('http://192.168.43.151/masta/'.$picture->getWebPath());
         $picture->setWebPath('http://localhost/masta/'.$picture->getWebPath());
      }
      else
      {
        $picture->setWebPath('http://localhost/masta/'.$picture->getWebPath());
      }
      
    }

    public function checkCategories($categories)
    {
      foreach ($categories as $category)
      {
        $this->checkCategory($category);
      }
    }

    public function checkCategory($category)
    {
          $user = $this->tokenStorage->getToken()->getUser();
          $category->setIsFollow(false);
          $category->setIsAuthent(false);
          if($category->getPicture() != null) $this->checkPicture($category->getPicture());
          if($user != NULL)
          {
            $category->setIsAuthent(true);
            foreach($category->getCategoryFollowers() as $categoryFollower )
            {
                if($categoryFollower->getAuthor() == $user)
                {
                  $category->setIsFollow(true);
                  break;
                }
            }
          }
    }

}
