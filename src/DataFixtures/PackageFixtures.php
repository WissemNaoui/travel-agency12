<?php

namespace App\DataFixtures;

use App\Entity\Package;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PackageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $destinations = [
            'Paris' => ['France', 'Experience the City of Light', 1200],
            'Rome' => ['Italy', 'Discover Ancient History', 1100],
            'Barcelona' => ['Spain', 'Art and Architecture Paradise', 950],
            'Amsterdam' => ['Netherlands', 'Canal City Adventure', 850],
            'Prague' => ['Czech Republic', 'Medieval City Exploration', 800],
            'Vienna' => ['Austria', 'Classical Music and Culture', 900],
            'London' => ['UK', 'British Heritage Tour', 1300],
            'Berlin' => ['Germany', 'Modern History Journey', 850],
            'Athens' => ['Greece', 'Ancient Greece Discovery', 950],
            'Venice' => ['Italy', 'Romantic Canal City', 1000]
        ];

        foreach ($destinations as $city => [$country, $description, $price]) {
            $package = new Package();
            $package->setName("$city Adventure Package")
                   ->setDescription($description)
                   ->setDestination($city)
                   ->setCountry($country)
                   ->setDuration(random_int(3, 10))
                   ->setPrice($price)
                   ->setMaxParticipants(random_int(10, 30))
                   ->setAvailableSpots(random_int(1, 10))
                   ->setStartDate(new \DateTime('+' . random_int(1, 30) . ' days'))
                   ->setEndDate(new \DateTime('+' . random_int(31, 60) . ' days'))
                   ->setIncludedServices(['Flight', 'Hotel', 'Breakfast', 'City Tour'])
                   ->setHighlights(['City Center Tour', 'Museum Visits', 'Local Cuisine Experience'])
                   ->setStatus('available');

            $manager->persist($package);
        }

        $manager->flush();
    }
}
