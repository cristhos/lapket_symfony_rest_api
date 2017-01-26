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

  private $tokenStorage;

  // On injecte l'EntityManager
  public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
  {
    $this->em = $em;
    $this->tokenStorage = $tokenStorage;
  }

  public function viewIts($products){
      $user = $this->tokenStorage->getToken()->getUser();
        if($user instanceOf User){
             foreach ($products as $product) {
                $this->viewIt($product);
            }
        }
     
  }

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

  //enregistrement de la vue
  public function persistProductView(Product $product,$user)
  {
     $productView = new ProductView();
      $productView->setProduct($product);
      $productView->setAuthor($user);
      $this->em->persist($productView);
      $this->em->flush();
  }
}