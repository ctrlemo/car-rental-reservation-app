<?php

namespace App\Entity;

//external
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

//local
use App\Repository\ReservationRepository;
use App\Entity\Vehicle;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;
    /*
    * TODO:
    * - The id type can be changed to a non-integer non-incremental type
    * - if needed, that is not easily guessable to improve security.
    * - For example, a UUID or ULID or a hash of the user email and the date of the reservation.
    * - This will make it harder for attackers to guess the id of a reservation
    * - but remember that UUID and ULID are not sequential and can cause performance issues
    * - in some databases, especially if you have a lot of reservations.
    * - So, use it wisely.
    * - You might also consider using a combination of a timestamp and a counter, or using a hashing function to generate unique identifiers.
    */

    #[ORM\Column(type: 'date')]
    #[Assert\NotNull(message: 'The start date cannot be null.')]
    #[Assert\NotBlank(message: 'The start date cannot be blank.')]
    #[Assert\GreaterThanOrEqual(
        value: 'today',
        message: 'The start date must be today or a future date.'
    )]
    private \DateTimeInterface $startDate;

    #[ORM\Column(type: 'date')]
    #[Assert\NotNull(message: 'The end date cannot be null.')]
    #[Assert\NotBlank(message: 'The end date cannot be blank.')]
    #[Assert\GreaterThan(
        propertyPath: 'startDate',
        message: 'The end date must be greater than the start date.'
    )]
    private \DateTimeInterface $endDate;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true, 'min' => 1])]
    #[Assert\Range(min: 1, notInRangeMessage: 'The passenger count must be at least {{ min }}.')]
    private int $passengerCount;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank(message: 'The email cannot be blank.')]
    #[Assert\Email(message: 'The email "{{ value }}" is not a valid email.')]
    private string $userEmail;

    #[ORM\ManyToOne(targetEntity: Vehicle::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Vehicle $vehicle;

    public function getId(): int
    {
        return $this->id;
    }
    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }
    public function setStartDate(\DateTimeInterface $startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }
    public function getEndDate(): \DateTimeInterface
    {
        return $this->endDate;
    }
    public function setEndDate(\DateTimeInterface $endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }
    public function getPassengerCount(): int
    {
        return $this->passengerCount;
    }
    public function setPassengerCount(int $passengerCount)
    {
        $this->passengerCount = $passengerCount;

        return $this;
    }
    public function getUserEmail(): string
    {
        return $this->userEmail;
    }
    public function setUserEmail(string $userEmail)
    {
        $this->userEmail = $userEmail;

        return $this;
    }
    public function getVehicle(): Vehicle
    {
        return $this->vehicle;
    }
    public function setVehicle(Vehicle $vehicle)
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    public function getTotalDays(): int
    {
        return $this->endDate->diff($this->startDate)->days;
    }
    
    public function getTotalPrice(): int
    {
        return $this->getTotalDays() * $this->vehicle->getPricePerDay();
    }

    public function getTotalPriceConverted(int $currencyUnits = 100): float
    {
        return $this->getTotalDays() * $this->vehicle->getPricePerDayConverted($currencyUnits);
    }

    public function __toString(): string
    {
        return sprintf(
            'Reservation (ID: %d, Vehicle: %s, Start Date: %s, End Date: %s, Passenger Count: %d, User Email: %s)',
            $this->id ?? 'N/A',
            $this->vehicle ?? 'N/A',
            $this->startDate->format('Y-m-d'),
            $this->endDate->format('Y-m-d'),
            $this->passengerCount,
            $this->userEmail
        );
    }
}