<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Event;
use App\Entity\Place;
use App\Entity\Artiste;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    /**
     * Faker Generator
     *
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $artisteList = [];
        for ($i = 1; $i <= 10; $i++) {

            $artiste = new Artiste();
            $artiste->setArtistName($this->faker->realText($maxNbChars = 10, $indexSize = 1))
                ->setArtistCategory($this->faker->word())
                ->setStatus(true);
            $artisteList[] = $artiste;

            $event = new Event();
            $event->setEventName($this->faker->realText($maxNbChars = 20, $indexSize = 1))
                ->setEventDate($this->faker->dateTimeBetween('now', '+6 months'))
                ->setArtist($artisteList[array_rand($artisteList)])
                ->setStatus(true);
            $manager->persist($event);
        }

        $place = new Place();
        $place->setPlaceName("Halle Tony Garnier")
            ->setPlaceAddress("20 place Docteurs Charles et Christophe Mérieux - 69007 Lyon 7ème")
            ->setPlaceRegion("Rhône-Alpes")
            ->setStatus(true);
        $manager->persist($place);

        $place1 = new Place();
        $place1->setPlaceName("Ninkasi Gerland")
            ->setPlaceAddress("267 rue Marcel Mérieux - 69007 Lyon 7ème")
            ->setPlaceRegion("Rhône-Alpes")
            ->setStatus(true);
        $manager->persist($place1);
        $place2 = new Place();
        $place2->setPlaceName("Le Transbordeur")
            ->setPlaceAddress("1/3 boulevard Stalingrad - 69100 Villeurbanne")
            ->setPlaceRegion("Rhône-Alpes")
            ->setStatus(true);
        $manager->persist($place2);

        $manager->flush();
    }
}
