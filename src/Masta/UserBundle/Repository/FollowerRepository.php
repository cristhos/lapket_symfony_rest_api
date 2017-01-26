<?php

namespace Masta\UserBundle\Repository;
use Doctrine\ORM\EntityRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
/**
 * FollowerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FollowerRepository extends EntityRepository
{
  public function getFollowers($limit, $page)
  {
      $queryBuilder = $this->createQueryBuilder('f')->getQuery();
      $pagerAdapter = new DoctrineORMAdapter($queryBuilder);
      $pager = new Pagerfanta($pagerAdapter);
      $pager->setCurrentPage($page);
      $pager->setMaxPerPage($limit);

      return $pager;
  }
  public function getFollowersAuthor($limit, $page, $author)
  {
      $queryBuilder = $this->createQueryBuilder('f')
                           ->where('f.userFollowed =:author')->setParameter('author', $author)
                           ->orderBy('f.publishedAt', 'DESC')
                           ->getQuery();
      $pagerAdapter = new DoctrineORMAdapter($queryBuilder);
      $pager = new Pagerfanta($pagerAdapter);
      $pager->setCurrentPage($page);
      $pager->setMaxPerPage($limit);

      return $pager;
  }
  public function getFollowingAuthor($limit, $page, $author)
  {
      $queryBuilder = $this->createQueryBuilder('f')
                           ->where('f.author =:author')->setParameter('author', $author)
                           ->orderBy('f.publishedAt', 'DESC')
                           ->getQuery();
      $pagerAdapter = new DoctrineORMAdapter($queryBuilder);
      $pager = new Pagerfanta($pagerAdapter);
      $pager->setCurrentPage($page);
      $pager->setMaxPerPage($limit);

      return $pager;
  }
}
