<?php

namespace App\DataFixtures;

use App\Entity\Flight;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FlightFixtures extends Fixture
{
    private const AIRLINES = [
        'Air France',
        'Lufthansa',
        'British Airways',
        'KLM',
        'Emirates',
        'Turkish Airlines',
        'Qatar Airways',
        'Swiss Air'
    ];

    private const CITIES = [
        'Paris',
        'London',
        'Berlin',
        'Amsterdam',
        'Rome',
        'Madrid',
        'Barcelona',
        'Vienna',
        'Prague',
        'Athens'
    ];

    public function load(ObjectManager $manager): void
    {
        $flightNumber = 1000;
        
        // Generate flights for the next 30 days
        for ($day = 0; $day < 30; $day++) {
            foreach (self::CITIES as $origin) {
                foreach (self::CITIES as $destination) {
                    if ($origin === $destination) {
                        continue;
                    }

                    // Create 2-3 flights per route per day
                    $flightsPerDay = random_int(2, 3);
                    for ($i = 0; $i < $flightsPerDay; $i++) {
                        $flight = new Flight();
                        
                        // Generate departure time between 6 AM and 10 PM
                        $hour = random_int(6, 22);
                        $minute = random_int(0, 11) * 5; // Round to nearest 5 minutes
                        
                        $departureTime = new \DateTime("+$day days");
                        $departureTime->setTime($hour, $minute);
                        
                        // Flight duration between 1 and 4 hours
                        $duration = random_int(60, 240);
                        $arrivalTime = clone $departureTime;
                        $arrivalTime->modify("+$duration minutes");

                        $flight->setFlightNumber(sprintf('FL%04d', $flightNumber++))
                              ->setOrigin($origin)
                              ->setDestination($destination)
                              ->setDepartureTime($departureTime)
                              ->setArrivalTime($arrivalTime)
                              ->setAirline(self::AIRLINES[array_rand(self::AIRLINES)])
                              ->setPrice(random_int(150, 1500))
                              ->setAvailableSeats(random_int(0, 200))
                              ->setStatus('scheduled');

                        $manager->persist($flight);
                    }
                }
            }
        }

        $manager->flush();
    }
}
