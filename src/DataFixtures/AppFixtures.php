<?php

namespace App\DataFixtures;

//external
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

//internal
use Psr\Log\LoggerInterface;
use Exception;

//local
use App\Enum\VehicleType;
use App\Enum\VehicleStatus;
use App\Entity\Vehicle;

class AppFixtures extends Fixture
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        try {
            // Create an array of vehicles for each type and include capacity, price, fuel percentage, and status
            $vehicles = [
                [
                    'type' => VehicleType::SEDAN,
                    'capacity' => 5,
                    'pricePerDay' => 50,
                    'fuelPercentage' => 100,
                    'status' => VehicleStatus::AVAILABLE,
                ],
                [
                    'type' => VehicleType::CONVERTIBLE,
                    'capacity' => 2,
                    'pricePerDay' => 70,
                    'fuelPercentage' => 100,
                    'status' => VehicleStatus::AVAILABLE,
                ],
                [
                    'type' => VehicleType::MINIVAN,
                    'capacity' => 7,
                    'pricePerDay' => 80,
                    'fuelPercentage' => 100,
                    'status' => VehicleStatus::AVAILABLE,
                ],
            ];


            // Loop through the array and create Vehicle entities
            foreach ($vehicles as $vehicleData) {
                // create 3 entries for each vehicle in the database
                for ($i = 0; $i < 3; $i++) {
                    // Create a new Vehicle entity
                    $vehicle = new Vehicle();
                    $vehicle->setType($vehicleData['type']);
                    $vehicle->setCapacity($vehicleData['capacity']);
                    $vehicle->setPricePerDay($vehicleData['pricePerDay']);
                    $vehicle->setFuelPercentage($vehicleData['fuelPercentage']);
                    $vehicle->setStatus($vehicleData['status']);

                    // Persist the vehicle entity
                    $manager->persist($vehicle);
                }
            }


            $manager->flush();
            // Flush the persisted entities to the database
            // This will execute all the SQL queries to insert the data into the database
            // and commit the transaction
            // This is important to ensure that the data is saved
            // and that the database is in a consistent state 
        } catch (Exception $e) {
            // Log the error
            $this->logger->error('An error occurred while loading fixtures: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
        }
    }
}
