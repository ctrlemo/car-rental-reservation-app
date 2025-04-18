<?php

declare(strict_types=1);

namespace App\Service;

//external
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
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
        private int $minRentalDays = 1,
        private int $maxRentalDays = 30,
        private int $cooldownDays = 7 // one rental per Z days
    ) {}

    /**
     * Find available vehicles based on passenger count and rental period.
     *
     * @param int $passengerCount The number of passengers.
     * @param DateTimeInterface $startDate The start date of the rental period.
     * @param DateTimeInterface $endDate The end date of the rental period.
     * @param string $userEmail The user's email address for reservation tracking.
     * @return Vehicle[]| Returns an array of available vehicles or null if no vehicles are found.
     */
    public function findAvailableVehicles(
        int $passengerCount,
        DateTime $startDate,
        DateTime $endDate,
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
                'error' => true,
                'message' => (string) $violations,
            ];
        }

        // 2. Enforce cooldown period
        $recentReservations = $this->reservationRepository->findByUserEmailInRange(
            $userEmail,
            (new DateTime($startDate->format('Y-m-d H:i:s')))->sub(new DateInterval("P{$this->cooldownDays}D")),
            $endDate
        );

        $violations = $validator->validate($recentReservations, [
            new Assert\Count([
                'min' => 0,
                'max' => 0,
                'notInRangeMessage' => 'You have a reservation within the cooldown period.',
            ]),
        ]);
        if (count($violations) > 0) {
            return [
                'error' => true,
                'message' => (string) $violations,
            ];
        }

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

        $altVehicles = $this->findAvailableVehicles($passengerCount, $altStart, $altEnd, $userEmail);

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
