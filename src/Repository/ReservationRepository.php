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
     * Find reservations by vehicle ID within a date range.
     *
     * @param int $vehicleId The vehicle ID.
     * @param DateTimeInterface $start The start date of the range.
     * @param DateTimeInterface $end The end date of the range.
     * @return Reservation[] Returns an array of Reservation objects.
     */
    public function findByVehicleInRange(int $vehicleId, DateTimeInterface $start, DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.vehicle = :vehicleId')
            ->andWhere('r.endDate BETWEEN :start AND :end')
            ->setParameter('vehicleId', $vehicleId)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
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