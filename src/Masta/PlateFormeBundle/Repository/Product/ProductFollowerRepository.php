<?php
namespace Masta\PlateFormeBundle\Repository\Product;

use Doctrine\ORM\EntityRepository;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * ProductFollower Repository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductFollowerRepository extends EntityRepository
{
    public function getProductFollowers($limit, $page, $product_id)
    {
        $queryBuilder = $this->createQueryBuilder('pf')
            ->leftJoin('pf.product', 'p')->addSelect('p')
            ->where('p.id = :product_id')->setParameter('product_id', $product_id)
            ->getQuery();
        $pagerAdapter = new DoctrineORMAdapter($queryBuilder);
        $pager = new Pagerfanta($pagerAdapter);
        $pager->setCurrentPage($page);
        $pager->setMaxPerPage($limit);

        return $pager;
    }

}