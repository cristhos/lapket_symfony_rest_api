<?php

namespace Masta\PlateFormeBundle\Repository\Deal;

use Doctrine\ORM\EntityRepository;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * ConversationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ConversationRepository extends EntityRepository
{
  //toutes les convesation de l'utilisateur actuel
  public function getMyConversations($limit, $page,$user){

    $queryBuilder = $this->createQueryBuilder('c')
                          ->leftJoin('c.product', 'p')->addSelect('p')
                          ->where('p.author =:author')->setParameter('author', $user)
                          ->orWhere('c.author =:author')->setParameter('author', $user)
                          ->orderBy('c.publishedAt', 'DESC')
                          ->getQuery();
    $pagerAdapter = new DoctrineORMAdapter($queryBuilder);
    $pager = new Pagerfanta($pagerAdapter);
    $pager->setMaxPerPage($limit);
    $pager->setCurrentPage($page);
    
    return $pager;
  }

  public function getConversationAuthorProduct($user,$product){
    $queryBuilder = $this->createQueryBuilder('c')
                          ->where('c.product =:product')->setParameter('product', $product)
                          ->andWhere('c.author =:author')->setParameter('author', $user)
                          ->orderBy('c.publishedAt', 'DESC')
                          ->getQuery();

    return $queryBuilder->getResult();
  }
}