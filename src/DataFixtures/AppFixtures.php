<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Faker\Factory;
use Faker\Generator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * Faker Generator
     *
     * @var Generator
     */
    private Generator $faker;

    public function __construct(){
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        for($i = 1; $i <= 10; $i++){
            $event = new Event();
            $event->setEventName($this->faker->realText($maxNbChars = 20, $indexSize = 1))
                ->setEventAdress($this->faker->address())
                ->setEventDate($this->faker->dateTimeBetween('now', '+6 months'))
                ->setStatus(1);
            $manager->persist($event);

        }
        
        $manager->flush();
    }
}
