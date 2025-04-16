<?php

namespace App\Repository;

//external
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

//local
use App\Entity\Reservation;


/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
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