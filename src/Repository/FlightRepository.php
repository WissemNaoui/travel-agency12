<?php

namespace App\Repository;

use App\Entity\Flight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Flight>
 *
 * @method Flight|null find($id, $lockMode = null, $lockVersion = null)
 * @method Flight|null findOneBy(array $criteria, array $orderBy = null)
 * @method Flight[]    findAll()
 * @method Flight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FlightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Flight::class);
    }

    public function findBySearchCriteria(array $criteria): array
    {
        $qb = $this->createQueryBuilder('f')
            ->where('1=1');

        if (!empty($criteria['origin'])) {
            $qb->andWhere('f.origin LIKE :origin')
               ->setParameter('origin', '%' . $criteria['origin'] . '%');
        }

        if (!empty($criteria['destination'])) {
            $qb->andWhere('f.destination LIKE :destination')
               ->setParameter('destination', '%' . $criteria['destination'] . '%');
        }

        if (!empty($criteria['departureDate'])) {
            $startDate = new \DateTime($criteria['departureDate']);
            $endDate = clone $startDate;
            $endDate->modify('+1 day');
            
            $qb->andWhere('f.departureTime >= :startDate')
               ->andWhere('f.departureTime < :endDate')
               ->setParameter('startDate', $startDate)
               ->setParameter('endDate', $endDate);
        }

        if (isset($criteria['minPrice'])) {
            $qb->andWhere('f.price >= :minPrice')
               ->setParameter('minPrice', $criteria['minPrice']);
        }

        if (isset($criteria['maxPrice'])) {
            $qb->andWhere('f.price <= :maxPrice')
               ->setParameter('maxPrice', $criteria['maxPrice']);
        }

        if (!empty($criteria['airline'])) {
            $qb->andWhere('f.airline LIKE :airline')
               ->setParameter('airline', '%' . $criteria['airline'] . '%');
        }

        if (isset($criteria['minSeats'])) {
            $qb->andWhere('f.availableSeats >= :minSeats')
               ->setParameter('minSeats', $criteria['minSeats']);
        }

        $qb->orderBy('f.departureTime', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findAvailableFlights(): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.availableSeats > 0')
            ->andWhere('f.departureTime > :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('f.departureTime', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function save(Flight $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Flight $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
