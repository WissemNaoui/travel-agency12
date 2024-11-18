<?php

namespace App\DataFixtures;

use App\Entity\Flight;
use App\Entity\Hotel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TravelFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create Flights
        $flights = [
            [
                'flightNumber' => 'TN507',
                'airline' => 'Tunisair',
                'origin' => 'TUN',
                'destination' => 'CDG',
                'departureTime' => new \DateTime('2024-01-15 10:00:00'),
                'arrivalTime' => new \DateTime('2024-01-15 13:30:00'),
                'price' => 450.00,
                'availableSeats' => 120,
                'status' => 'SCHEDULED'
            ],
            [
                'flightNumber' => 'TN508',
                'airline' => 'Tunisair',
                'origin' => 'CDG',
                'destination' => 'TUN',
                'departureTime' => new \DateTime('2024-01-15 15:00:00'),
                'arrivalTime' => new \DateTime('2024-01-15 18:30:00'),
                'price' => 480.00,
                'availableSeats' => 90,
                'status' => 'SCHEDULED'
            ],
            [
                'flightNumber' => 'NZ103',
                'airline' => 'Nouvelair',
                'origin' => 'TUN',
                'destination' => 'ORY',
                'departureTime' => new \DateTime('2024-01-16 08:30:00'),
                'arrivalTime' => new \DateTime('2024-01-16 12:00:00'),
                'price' => 420.00,
                'availableSeats' => 75,
                'status' => 'SCHEDULED'
            ],
            [
                'flightNumber' => 'NZ104',
                'airline' => 'Nouvelair',
                'origin' => 'ORY',
                'destination' => 'TUN',
                'departureTime' => new \DateTime('2024-01-16 14:00:00'),
                'arrivalTime' => new \DateTime('2024-01-16 17:30:00'),
                'price' => 440.00,
                'availableSeats' => 100,
                'status' => 'SCHEDULED'
            ],
        ];

        foreach ($flights as $flightData) {
            $flight = new Flight();
            $flight->setFlightNumber($flightData['flightNumber']);
            $flight->setAirline($flightData['airline']);
            $flight->setOrigin($flightData['origin']);
            $flight->setDestination($flightData['destination']);
            $flight->setDepartureTime($flightData['departureTime']);
            $flight->setArrivalTime($flightData['arrivalTime']);
            $flight->setPrice($flightData['price']);
            $flight->setAvailableSeats($flightData['availableSeats']);
            $flight->setStatus($flightData['status']);
            
            $manager->persist($flight);
        }

        // Create Hotels
        $hotels = [
            [
                'name' => 'Laico Tunis',
                'description' => 'Luxury hotel in the heart of Tunis with stunning city views.',
                'address' => 'Avenue Mohamed V',
                'city' => 'Tunis',
                'country' => 'Tunisia',
                'rating' => '5',
                'pricePerNight' => 250.00,
                'availableRooms' => 50,
                'amenities' => ['WiFi', 'Pool', 'Spa', 'Restaurant', 'Gym'],
                'website' => 'https://laicotunis.com',
                'phoneNumber' => '+216 71 830 000'
            ],
            [
                'name' => 'Movenpick Gammarth',
                'description' => 'Beachfront resort with Mediterranean charm.',
                'address' => 'BP 36 La Marsa',
                'city' => 'Gammarth',
                'country' => 'Tunisia',
                'rating' => '5',
                'pricePerNight' => 300.00,
                'availableRooms' => 40,
                'amenities' => ['Beach Access', 'Pool', 'Spa', 'Restaurant', 'Bar'],
                'website' => 'https://movenpick-gammarth.com',
                'phoneNumber' => '+216 71 741 444'
            ],
            [
                'name' => 'The Residence Tunis',
                'description' => 'Elegant beachfront hotel with world-class amenities.',
                'address' => 'Les Côtes de Carthage',
                'city' => 'Gammarth',
                'country' => 'Tunisia',
                'rating' => '5',
                'pricePerNight' => 350.00,
                'availableRooms' => 30,
                'amenities' => ['Private Beach', 'Golf Course', 'Spa', 'Fine Dining'],
                'website' => 'https://theresidence-tunis.com',
                'phoneNumber' => '+216 71 910 101'
            ],
            [
                'name' => 'Dar El Jeld Hotel & Spa',
                'description' => 'Boutique hotel in the heart of the Medina.',
                'address' => '5-10 Rue Dar El Jeld',
                'city' => 'Tunis',
                'country' => 'Tunisia',
                'rating' => '4',
                'pricePerNight' => 200.00,
                'availableRooms' => 20,
                'amenities' => ['Spa', 'Restaurant', 'Rooftop Terrace', 'WiFi'],
                'website' => 'https://dareljeld-hotel.com',
                'phoneNumber' => '+216 71 524 488'
            ],
        ];

        foreach ($hotels as $hotelData) {
            $hotel = new Hotel();
            $hotel->setName($hotelData['name']);
            $hotel->setDescription($hotelData['description']);
            $hotel->setAddress($hotelData['address']);
            $hotel->setCity($hotelData['city']);
            $hotel->setCountry($hotelData['country']);
            $hotel->setRating($hotelData['rating']);
            $hotel->setPricePerNight($hotelData['pricePerNight']);
            $hotel->setAvailableRooms($hotelData['availableRooms']);
            $hotel->setAmenities($hotelData['amenities']);
            $hotel->setWebsite($hotelData['website']);
            $hotel->setPhoneNumber($hotelData['phoneNumber']);
            
            $manager->persist($hotel);
        }

        $manager->flush();
    }
}
