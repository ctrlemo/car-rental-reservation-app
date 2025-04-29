<?php

declare(strict_types=1);

namespace App\Service;

//external
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Psr\Log\LoggerInterface;
use DateInterval;
use DateTimeInterface;
use DateTime;

//local
use App\Entity\Vehicle;
use App\Entity\Reservation;
use App\Repository\VehicleRepository;
use App\Repository\ReservationRepository;

class VehicleSelectorService
{
    public function __construct(
        private VehicleRepository $vehicleRepository,
        private ReservationRepository $reservationRepository,
        private EntityManagerInterface $em,
        private int $minRentalDays,
        private int $maxRentalDays,
        private int $cooldownDays, // one rental per Z days
        private LoggerInterface $logger,
    ) {}

    /**
     * Find available vehicles based on passenger count and rental period.
     *
     * @param DateTimeInterface $startDate The start date of the rental period.
     * @param DateTimeInterface $endDate The end date of the rental period.
     * @param int $passengerCount The number of passengers.
     * @param string $userEmail The user's email address for reservation tracking.
     * @return Vehicle[]| Returns an array of available vehicles or null if no vehicles are found.
     */
    public function findAvailableVehicles(
        DateTime $startDate,
        DateTime $endDate,
        int $passengerCount,
        string $userEmail
    ): array {
        $validator = Validation::createValidator();

        // 1. Validate rental period
        $interval = $startDate->diff($endDate)->days;
        $violations = $validator->validate($interval, [
            new Assert\Range([
                'min' => $this->minRentalDays,
                'max' => $this->maxRentalDays,
                'notInRangeMessage' => 'The rental period must be between {{ min }} and {{ max }} days.',
            ]),
        ]);
    
        if (count($violations) > 0) {
            return [
                'warning' => true,
                'message' => (string) $violations,
            ];
        }

        // 2. Enforce cooldown period
        $recentReservations = $this->reservationRepository->findByUserEmailInRange(
            $userEmail,
            (new DateTime($startDate->format('Y-m-d')))->sub(new DateInterval("P{$this->cooldownDays}D")),
            $endDate
        );

        $violations = $validator->validate($recentReservations, [
            new Assert\Count([
                'min' => 0,
                'max' => 0,
                'maxMessage' => 'You have a reservation within the cooldown period.',
            ]),
        ]);
        if (count($violations) > 0) {
            return [
                'warning' => true,
                'message' => (string) $violations,
            ];
        }

        /*
        * TODO: Step 3 and 4 can be combined into a single query to improve performance.
        * The single query can be moved to the database as a stored procedure, which will
        * perform better.
        */

        // 3. Get all vehicles that match capacity and are available by status
        $vehicles = $this->vehicleRepository->findVehiclesByCapacityAndStatus($passengerCount);

        $availableVehicles = [];
        // 4. Filter out vehicles that are already reserved
        foreach ($vehicles as $vehicle) {
            $reservations = $this->reservationRepository->findByVehicleInRange(
                $vehicle->getId(),
                $startDate,
                $endDate
            );
            if (count($reservations) === 0) {
                $availableVehicles[] = $vehicle;
            }
        }

        // 4. Sort by price
        usort($availableVehicles, fn(Vehicle $a, Vehicle $b) => $a->getPricePerDay() <=> $b->getPricePerDay());

        return $availableVehicles ?? [];
    }

    public function suggestAlternativeDates(
        int $passengerCount,
        DateTime $startDate,
        string $userEmail,
        int $shiftDays = 3,
    ): array {
        // Calculate alternative start and end dates
        $altStart = (clone $startDate)->modify("+{$shiftDays} days");
        $altEnd = (clone $altStart)->modify("+{$this->minRentalDays} days");

        $altVehicles = $this->findAvailableVehicles($altStart, $altEnd, $passengerCount, $userEmail);

        if ($altVehicles) {
            return [
                'vehicles' => $altVehicles,
                'start' => $altStart,
                'end' => $altEnd,
            ];
        }

        return [];
    }

    /** get all vehicles
     * @return Vehicle[] Returns an array of all vehicles.
     */
    public function getAllVehicles(): array
    {
        $this->logger->info('findAllVehicles called');
        $this->logger->info('minRentalDays: ' . $this->minRentalDays);
        $this->logger->info('maxRentalDays: ' . $this->maxRentalDays);
        $this->logger->info('cooldownDays: ' . $this->cooldownDays);
        
        return $this->vehicleRepository->findAll();
    }
    

    /**
     * Get all reservations.
     *
     * @return Reservation[] Returns an array of all reservations.
     */
    public function getAllReservations(): array
    {
        return $this->reservationRepository->findAll();
    }
}
