<?php

namespace App\Repository;

use App\Entity\Product;
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

    public function add(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getLastProductVersion(): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT 
                max(p.version)
            FROM 
                App\Entity\Product p'
        );

        return $query->getOneOrNullResult();
    }

    public function getAllProductsFiltered(
        \DateTime $version,
        ?string $location,
        ?int $minStorage,
        ?int $maxStorage,
        ?array $ram,
        ?string $hardDiskType,
        ?int $firstRow,
        ?int $maxRows,
    ): array {
        $entityManager = $this->getEntityManager();
        $params = ['version' => $version];
        $where = ['p.version = :version'];

        if ($location) {
            array_push($where, 'l.name = :location');
            $params['location'] = $location;
        }

        if ($minStorage >= 0 && $maxStorage >= 1) {
            if ($minStorage == 0) {
                array_push($where, '(s.size BETWEEN ?1 AND ?2 OR s.size IS NULL)');
            } else {
                array_push($where, 's.size BETWEEN ?1 AND ?2');
            }
            $params[1] = $minStorage;
            $params[2] = $maxStorage;
        }

        if ($ram && is_array($ram) && count($ram) > 0) {
            array_push($where, 'r.size IN (:ram)');
            $params['ram'] = $ram;
        }

        if ($hardDiskType) {
            array_push($where, 's.type = :hardDiskType');
            $params['hardDiskType'] = $hardDiskType;
        }

        $sql = '
            SELECT
                p.id, p.price, p.currency,
                m.name as model,
                r.name as ram,
                s.name as storage,
                l.name as location
            FROM
                App\Entity\Product p
            INNER JOIN
                p.model m
            INNER JOIN
                p.ram r
            LEFT JOIN
                p.storage s
            INNER JOIN
                p.location l
        ';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY p.id ASC';

        $query = $entityManager
            ->createQuery($sql)
            ->setParameters($params)
            ->setFirstResult($firstRow)
            ->setMaxResults($maxRows);

        return $query->getResult();
    }

    public function getAllProductsFilteredCount(
        \DateTime $version,
        ?string $location,
        ?int $minStorage,
        ?int $maxStorage,
        ?array $ram,
        ?string $hardDiskType
    ): int {
        $entityManager = $this->getEntityManager();
        $params = ['version' => $version];
        $where = ['p.version = :version'];

        if ($location) {
            array_push($where, 'l.name = :location');
            $params['location'] = $location;
        }

        if ($minStorage >= 0 && $maxStorage >= 1) {
            if ($minStorage == 0) {
                array_push($where, '(s.size BETWEEN ?1 AND ?2 OR s.size IS NULL)');
            } else {
                array_push($where, 's.size BETWEEN ?1 AND ?2');
            }
            $params[1] = $minStorage;
            $params[2] = $maxStorage;
        }

        if ($ram && is_array($ram) && count($ram) > 0) {
            array_push($where, 'r.size IN (:ram)');
            $params['ram'] = $ram;
        }

        if ($hardDiskType) {
            array_push($where, 's.type = :hardDiskType');
            $params['hardDiskType'] = $hardDiskType;
        }

        $sql = '
            SELECT
                count(p.id) 
            FROM
                App\Entity\Product p
            INNER JOIN
                p.model m
            INNER JOIN
                p.ram r
            LEFT JOIN
                p.storage s
            INNER JOIN
                p.location l
        ';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $query = $entityManager
            ->createQuery($sql)
            ->setParameters($params);

        return $query->getSingleScalarResult();
    }

    public function getAllProductsLocations(
        \DateTime $version
    ): array {
        $entityManager = $this->getEntityManager();

        $sql = '
            SELECT                
                l.name as location
            FROM
                App\Entity\Product p
            INNER JOIN
                p.location l
            WHERE
                p.version = :version
            GROUP BY 
                l.id
        ';

        $query = $entityManager->createQuery($sql)->setParameter('version', $version);

        return $query->getResult();
    }


//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
