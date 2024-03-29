<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function getProductsByFilters($company = null, $options = [])
    {
        $query = $this->createQueryBuilder('p');

        if ($company) {
            $query->andWhere('p.company = :company')
                ->setParameter('company', $company);
        }

        if (isset($options['alias']) && $options['alias'] !== '') {
            $parts = explode(' ', $options['alias']);
            $subAnd = [];
            foreach ($parts as $k => $p) {
                $tag = 'alias_' . $k;
                $subOr = [];
                foreach (['p.name', 'p.slug'] as $f) {
                    $subOr[] = "{$f} LIKE :{$tag}";
                }
                $subAnd[] = '(' . implode(' OR ', $subOr) . ')';
                $query->setParameter($tag, "%$p%");
            }
            $query
                ->andWhere('(' . implode(' AND ', $subAnd) . ')');
        }
        if (isset($options['category']) && $options['category']) {
            $query->innerJoin('p.categories', 'categories')
                ->andWhere('categories.id IN (:category)')
                ->setParameter('category', $options['category']);
        }
        if (isset($options['price']) && $options['price']) {
            $query
                ->andWhere('p.price LIKE :price')
                ->setParameter('price', '%' . $options['price'] . '%');
        }

        return $query->getQuery()->getResult();
    }

    public function getWithoutCategory(Company $company)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.company = :company')
            ->andWhere('p.categories IS EMPTY')
            ->setParameter('company', $company)
            ->getQuery()
            ->getResult();
    }
}
