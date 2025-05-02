<?php

namespace App\Repository;

//external
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTimeInterface;

//local
use App\Entity\Vehicle;
use App\Enum\VehicleStatus;

/**
 * @extends ServiceEntityRepository<Vehicle>
 */
class VehicleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }

    public function findVehiclesByCapacityAndStatus(int $minCapacity, VehicleStatus $status = VehicleStatus::AVAILABLE ): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.capacity >= :minCapacity')
            ->andWhere('v.status = :status')
            ->setParameter('minCapacity', $minCapacity)
            ->setParameter('status', $status)
            ->orderBy('v.capacity', 'ASC')
            ->addOrderBy('v.pricePerDay', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('v')
            ->getQuery()
            ->getResult();
    }


//    /**
//     * @return Vehicle[] Returns an array of Vehicle objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Vehicle
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
