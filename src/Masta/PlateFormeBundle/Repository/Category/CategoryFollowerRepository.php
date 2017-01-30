<?php

namespace Masta\PlateFormeBundle\Repository\Category;

use Doctrine\ORM\EntityRepository;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
/**
 * CategoryFollowerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CategoryFollowerRepository extends EntityRepository
{
    public function getCategoryFollowers($limit, $page, $album_id)
    {
        $queryBuilder = $this->createQueryBuilder('cf')
            ->leftJoin('cf.category', 'c')->addSelect('c')
            ->where('c.id = :album_id')->setParameter('album_id', $album_id)
            ->orderBy('cf.publishedAt', 'DESC')
            ->getQuery();
        $pagerAdapter = new DoctrineORMAdapter($queryBuilder);
        $pager = new Pagerfanta($pagerAdapter);
        $pager->setCurrentPage($page);
        $pager->setMaxPerPage($limit);

        return $pager;
    }
}