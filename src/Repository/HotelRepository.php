<?php

namespace App\Repository;

use App\Entity\Hotel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Hotel>
 */
class HotelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hotel::class);
    }

    public function findBySearchCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder('h')
            ->andWhere('h.availableRooms > 0');

        if (!empty($criteria['city'])) {
            $qb->andWhere('LOWER(h.city) LIKE LOWER(:city)')
               ->setParameter('city', '%' . $criteria['city'] . '%');
        }

        // Add a check for country as well for better search results
        if (!empty($criteria['city'])) {
            $qb->orWhere('LOWER(h.country) LIKE LOWER(:country)')
               ->setParameter('country', '%' . $criteria['city'] . '%');
        }

        // Check room availability based on max guests if provided
        if (!empty($criteria['guests'])) {
            $qb->andWhere('h.maxGuests >= :guests')
               ->setParameter('guests', $criteria['guests']);
        }

        // Order by rating and price for better results
        $qb->orderBy('h.rating', 'DESC')
           ->addOrderBy('h.pricePerNight', 'ASC');

        return $qb->getQuery()->getResult();
    }

//    /**
//     * @return Hotel[] Returns an array of Hotel objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Hotel
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
