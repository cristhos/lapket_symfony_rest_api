<?php

namespace Masta\UserBundle\Repository;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

/**
 * CityRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CityRepository extends \Doctrine\ORM\EntityRepository
{
    public function getCities($limit, $page)
    {
        $queryBuilder = $this->createQueryBuilder('c')
                             ->orderBy('c.rank', 'DESC')
                             ->getQuery();
        $pagerAdapter = new DoctrineORMAdapter($queryBuilder);
        $pager = new Pagerfanta($pagerAdapter);
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);
    
        return $pager;
    }

    public function getCitiesInCountry($limit, $page,$country)
    {
        $queryBuilder = $this->createQueryBuilder('c')
                             ->where('c.country =:country')->setParameter('country', $country) 
                             ->orderBy('c.rank', 'DESC')
                             ->getQuery();
        $pagerAdapter = new DoctrineORMAdapter($queryBuilder);
        $pager = new Pagerfanta($pagerAdapter);
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);
    
        return $pager;
    }
}