<?php

namespace App\Entity;

//external
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

//local
use App\Enum\VehicleStatus;
use App\Enum\VehicleType;
use App\Repository\VehicleRepository;


#[ORM\Entity(repositoryClass: VehicleRepository::class)]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private VehicleType $type;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true, 'min' => 1])]
    #[Assert\Range(min: 1)]
    private int $capacity;

    #[ORM\Column]
    private int $pricePerDay;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true, 'min' => 0, 'max' => 100])]
    #[Assert\Range(min: 0, max: 100)]
    private int $fuelPercentage;

    #[ORM\Column]
    private VehicleStatus $status;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity)
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getPricePerDay(): int
    {
        return $this->pricePerDay;
    }

    public function setPricePerDay(int $pricePerDay)
    {
        $this->pricePerDay = $pricePerDay;

        return $this;
    }

    public function getFuelPercentage(): int
    {
        return $this->fuelPercentage;
    }
    public function setFuelPercentage(int $fuelPercentage)
    {
        $this->fuelPercentage = $fuelPercentage;

        return $this;
    }
    public function getType(): VehicleType
    {
        return $this->type;
    }
    public function setType(VehicleType $type)
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus(): VehicleStatus
    {
        return $this->status;
    }

    public function setStatus(VehicleStatus $status)
    {
        $this->status = $status;

        return $this;
    }

    // get price per day converted from currency units
    public function getPricePerDayConverted(int $currencyUnits = 100): float
    {
                return $this->pricePerDay / $currencyUnits;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s (ID: %d, Capacity: %d, Price per day: %d, Fuel percentage: %d)',
            $this->type->value,
            $this->id,
            $this->capacity,
            $this->pricePerDay,
            $this->fuelPercentage
        );
    }
}
