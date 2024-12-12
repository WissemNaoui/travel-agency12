<?php

namespace App\Repository;

use App\Entity\Package;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Package>
 */
class PackageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Package::class);
    }

    public function findBySearchCriteria(array $criteria, array $orderBy = null, $limit = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', $criteria['status'] ?? 'available');

        // Add destination filter if provided
        if (!empty($criteria['destination'])) {
            $qb->andWhere('LOWER(p.destination) LIKE LOWER(:destination)')
               ->setParameter('destination', '%' . $criteria['destination'] . '%');
        }

        // Add date range filter if provided
        if (!empty($criteria['start_date']) && !empty($criteria['end_date'])) {
            $qb->andWhere('p.start_date >= :start_date')
               ->andWhere('p.end_date <= :end_date')
               ->setParameter('start_date', $criteria['start_date'])
               ->setParameter('end_date', $criteria['end_date']);
        }

        // Add ordering
        if ($orderBy) {
            foreach ($orderBy as $field => $direction) {
                $qb->orderBy('p.' . $field, $direction);
            }
        }

        // Add limit
        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Package[] Returns an array of Package objects
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

//    public function findOneBySomeField($value): ?Package
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
