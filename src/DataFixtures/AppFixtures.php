<?php

namespace App\DataFixtures;

//external
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

//internal
use Psr\Log\LoggerInterface;
use Exception;
use DateTime;


//local
use App\Enum\VehicleType;
use App\Enum\VehicleStatus;
use App\Entity\Vehicle;
use App\Entity\Reservation;

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
                    'pricePerDay' => 5000,
                    'fuelPercentage' => 100,
                    'status' => VehicleStatus::AVAILABLE,
                ],
                [
                    'type' => VehicleType::CONVERTIBLE,
                    'capacity' => 2,
                    'pricePerDay' => 7000,
                    'fuelPercentage' => 100,
                    'status' => VehicleStatus::AVAILABLE,
                ],
                [
                    'type' => VehicleType::MINIVAN,
                    'capacity' => 7,
                    'pricePerDay' => 8000,
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


            // lets create a reservation for each vehicle type
            $startDate = new DateTime();
            foreach ($vehicles as $index => $vehicleData) {
                // Create a new Reservation entity
                $reservation = new Reservation();
                $reservation->setStartDate($startDate);
                $reservation->setEndDate((clone $startDate)->modify('+7 day'));
                $reservation->setPassengerCount($vehicleData['capacity']);
                $reservation->setUserEmail('user' . $index . '@example.com'); // Use the index for userEmail

                $vehicle = $manager->getRepository(Vehicle::class)->findOneBy([
                    'status' => $vehicleData['status']->value,
                    'type' => $vehicleData['type']->value,
                ]);
                
                $this->logger->info('Vehicle found: ' . $vehicle->getId());

                if ($vehicle) {
                    $reservation->setVehicle($vehicle);
                }

                // Persist the reservation entity
                $manager->persist($reservation);

                // Flush the persisted entities to the database
                // This will execute all the SQL queries to insert the data into the database
                // and commit the transaction
                // This is important to ensure that the data is saved
                // and that the database is in a consistent state
                $manager->flush();
            }


            // Log the successful loading of fixtures
            $this->logger->info('Fixtures loaded successfully.');

        } catch (Exception $e) {
            // Log the error
            $this->logger->error('An error occurred while loading fixtures: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
        }
    }
}
