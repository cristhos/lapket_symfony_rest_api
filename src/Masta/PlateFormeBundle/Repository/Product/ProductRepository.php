<?php

namespace Masta\PlateFormeBundle\Repository\Product;

use Doctrine\ORM\EntityRepository;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository extends EntityRepository
{

    public function getProducts($limit, $page)
    {
        $queryBuilder = $this->createQueryBuilder('p')
                             ->orderBy('p.publishedAt', 'DESC')
                             ->getQuery();
        $pagerAdapter = new DoctrineORMAdapter($queryBuilder);
        $pager = new Pagerfanta($pagerAdapter);
        $pager->setCurrentPage($page);
        $pager->setMaxPerPage($limit);

        return $pager;
    }

    public function getLastProducts($limit, $page)
    {
        $queryBuilder = $this->createQueryBuilder('p')
                             ->where('p.isBlocked =:blocked')->setParameter('blocked', false)
                             ->andWhere('p.isExpired =:expired')->setParameter('expired', false)
                             ->orderBy('p.rank', 'DESC')
                             ->getQuery();
        $pagerAdapter = new DoctrineORMAdapter($queryBuilder);
        $pager = new Pagerfanta($pagerAdapter);
        
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $pager;
    }

    public function getFilProducts($limit, $page, $user)
    {
      $follows_a = array();
      $categoryFollows = array();
      foreach ($user->getFollowers() as $follows) $follows_a[] = $follows->getUserFollowed();
      foreach ($user->getCategoryFollows() as $cf) $categoryFollows[] = $cf->getCategory();

        /*recupere tout les produit dont je suis l'auteur ou  qui appartiennent a mes abonnement
         , a ma ville, ou a une category 
         dont jesui abonnee qui ne sont pas bloquer et epuiser
         */
     $queryBuilder = $this->createQueryBuilder('p')
                          ->leftJoin('p.author', 'a')->addSelect('a')
                          ->where('p.author =:author')->setParameter('author', $user)
                          ->orWhere('p.category in(:categoryFollows)')->setParameter('categoryFollows', $categoryFollows)
                          ->orWhere('p.author in(:follows_a)')->setParameter('follows_a', $follows_a)
                          ->orWhere('a.city =:city')->setParameter('city', $user->getCity())
                          ->orWhere('a.country =:country')->setParameter('country', $user->getCountry())
                          ->andWhere('p.isBlocked =:blocked')->setParameter('blocked', false)
                          ->andWhere('p.isExpired =:expired')->setParameter('expired', false)
                          ->orderBy('p.rank', 'DESC')
                          ->getQuery();
        $pagerAdapter = new DoctrineORMAdapter($queryBuilder);
        $pager = new Pagerfanta($pagerAdapter);
        
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $pager;
    }


    public function getProductsByCategory($limit, $page, $category_id)
    {
      $qb = $this->createQueryBuilder('p');

      $qb->leftJoin('p.category', 'c')->addSelect('c')
                 ->where('c.id = :category_id')->setParameter('category_id', $category_id)
                 ->orderBy('p.publishedAt', 'DESC')
                           ->getQuery();
      $pagerAdapter = new DoctrineORMAdapter($qb);
      $pager = new Pagerfanta($pagerAdapter);
      
      $pager->setMaxPerPage($limit);
      $pager->setCurrentPage($page);


        return $pager;
    }

    public function getProductsByUser($limit, $page, $slug)
    {
        $queryBuilder = $this->createQueryBuilder('p')
        					 ->leftJoin('p.author', 'a')->addSelect('a')
        					 ->where('a.username = :slug')->setParameter('slug', $slug)
        					 ->orWhere('a.email = :slug')->setParameter('slug', $slug)
        					 ->orderBy('p.publishedAt', 'DESC')
                             ->getQuery();
        $pagerAdapter = new DoctrineORMAdapter($queryBuilder);
        $pager = new Pagerfanta($pagerAdapter);

        $pager->setMaxPerPage($limit);
         $pager->setCurrentPage($page);

        return $pager;
    }

    public function getProductsTerm($limit, $page, $term)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.description like :term')->setParameter('term','%'.$term.'%')
            ->orderBy('p.rank', 'DESC')
            ->getQuery();
        $pagerAdapter = new DoctrineORMAdapter($qb);
        $pager = new Pagerfanta($pagerAdapter);

        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $pager;
    }
    public function getProductsVoteByUser($limit, $page, $author)
    {
        $queryBuilder = $this->createQueryBuilder('p')
        					 ->leftJoin('p.votes', 'v')->addSelect('v')
        					 ->where('v.author = :author')->setParameter('author', $author)
        					 ->orderBy('v.publishedAt', 'DESC')
                             ->getQuery();
        $pagerAdapter = new DoctrineORMAdapter($queryBuilder);
        $pager = new Pagerfanta($pagerAdapter);
        
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $pager;
    }

    public function getProductsExpired($limit, $page, $user)
    {
        $queryBuilder = $this->createQueryBuilder('p')
        					 ->where('p.author = :author')->setParameter('author', $user)
                             ->andWhere('p.isExpired = :is_expired')->setParameter('is_expired', true)
        					 ->orderBy('v.publishedAt', 'DESC')
                             ->getQuery();
        $pagerAdapter = new DoctrineORMAdapter($queryBuilder);
        $pager = new Pagerfanta($pagerAdapter);
        
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        return $pager;
    }
}
