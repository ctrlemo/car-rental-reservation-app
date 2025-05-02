<?php

namespace App\Repository;

//external
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTimeInterface;

//local
use App\Entity\Reservation;


/**
 * @extends ServiceEntityRepository<Reservation>
 *
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Find reservations by user email within a date range.
     *
     * @param string $email The user's email address.
     * @param DateTimeInterface $start The start date of the range.
     * @param DateTimeInterface $end The end date of the range.
     * @return Reservation[] Returns an array of Reservation objects.
     */
    public function findByUserEmailInRange(string $email, DateTimeInterface $start, DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.userEmail = :email')
            ->andWhere('r.startDate BETWEEN :start AND :end')
            ->setParameter('email', $email)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find reservations by vehicle ID that overlap with a date range.
     *
     * This method retrieves reservations for a specific vehicle where the reservation's
     * end date is on or after the start of the given range, or the reservation's start date
     * is on or before the end of the given range.
     *
     * @param int $vehicleId The vehicle ID.
     * @param DateTimeInterface $start The start date of the range.
     * @param DateTimeInterface $end The end date of the range.
     * @return Reservation[] Returns an array of Reservation objects.
     */
    public function findOverlappingReservationsByVehicle(int $vehicleId, DateTimeInterface $start, DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('r')
            ->Where('r.vehicle = :vehicleId')
            ->andWhere('r.endDate >= :start OR r.startDate <= :end')
            ->setParameter('vehicleId', $vehicleId)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find the count of reservations by vehicle ID that overlap with a date range.
     *
     * @param int $vehicleId The vehicle ID.
     * @param DateTimeInterface $start The start date of the range.
     * @param DateTimeInterface $end The end date of the range.
     * @return int Returns the count of overlapping reservations.
     */
    public function countOverlappingReservationsByVehicle(int $vehicleId, DateTimeInterface $start, DateTimeInterface $end): int
    {
        return (int) $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('r.vehicle = :vehicleId')
            ->andWhere('r.endDate >= :start OR r.startDate <= :end')
            ->setParameter('vehicleId', $vehicleId)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();
    }
    
    /**
     * Find a reservation by its ID.
     *
     * @param int $id The reservation ID.
     * @return Reservation|null Returns the Reservation object or null if not found.
     */
    public function findOneById(int $id): ?Reservation
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
    /**
     * Find all reservations.
     *
     * @return Reservation[] Returns an array of Reservation objects.
     */
    public function findAllReservations(): array
    {
        return $this->createQueryBuilder('r')
            ->getQuery()
            ->getResult();
    }


    /**
     * Save a Reservation entity.
     */
    public function save(Reservation $reservation, bool $flush = false): void
    {
        $this->getEntityManager()->persist($reservation);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Remove a Reservation entity.
     */
    public function remove(Reservation $reservation, bool $flush = false): void
    {
        $this->getEntityManager()->remove($reservation);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Add custom query methods if needed
}