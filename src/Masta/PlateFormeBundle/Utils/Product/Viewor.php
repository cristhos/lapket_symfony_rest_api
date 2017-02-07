<?php
namespace Masta\PlateFormeBundle\Utils\Product;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;

use Masta\PlateFormeBundle\Entity\Product\Product;
use Masta\PlateFormeBundle\Entity\Product\ProductView;
use Masta\UserBundle\Entity\User;

class Viewor
{
  /**
   * @var EntityManagerInterface
   */
  private $em;

  /**
   * @var TokenStorageInterface
   */
  private $tokenStorage;

  // injecting EntityManager an TokenStorage
  public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
  {
    $this->em = $em;
    $this->tokenStorage = $tokenStorage;
  }

  //check  array();
  public function viewIts($products){
      $user = $this->tokenStorage->getToken()->getUser();
        if($user instanceOf User)
        {
             foreach ($products as $product) 
             {
                $this->viewIt($product);
            }
        }
     
  }

  //add in view list if your are not in 
  public function viewIt(Product $product)
  {
       $user = $this->tokenStorage->getToken()->getUser();

       $pass = true;
       foreach ($product->getProductViews() as $pv) 
       {
           if($pv->getAuthor() == $user)
           {
               $pass = false;
               break;
           }
       }

       if($pass)
       {
         $this->persistProductView($product,$user);
       }
  }

  //persiste post view
  public function persistProductView(Product $product,$user)
  {
      if($user instanceof User)
      {
           $productView = new ProductView();
           $productView->setProduct($product);
           $productView->setAuthor($user);
           $this->em->persist($productView);
           $this->em->flush();
      }
     
  }
}