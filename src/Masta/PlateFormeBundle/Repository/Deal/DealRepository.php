<?php

namespace Masta\PlateFormeBundle\Repository\Deal;
use Doctrine\ORM\EntityRepository;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * DealRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DealRepository extends EntityRepository
{
  //recupere tout le deal dont je suis l'autheur et qui appartient a un produit x
  public function getMyDealsByProduct($product, $user, $page,$limit){

  }

  //recupere tou le deal qui appartienne au produit dont je suis l'autheur
  public function getDealsProduct($limit, $page,$product_id,$user){

    $queryBuilder = $this->createQueryBuilder('d')
                         ->where('d.product =:product_id')->setParameter('product_id', $product_id)
                         ->andWhere('d.author =:author')->setParameter('author', $user)
                         ->orWhere('d.destinathor =:author')->setParameter('author', $user)
                         ->orderBy('d.publishedAt', 'ASC')
                         ->getQuery();
    $pagerAdapter = new DoctrineORMAdapter($queryBuilder);
    $pager = new Pagerfanta($pagerAdapter);
    $pager->setCurrentPage($page);
    $pager->setMaxPerPage($limit);

    return $pager;
  }


}
