<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Event;
use App\Entity\Place;
use App\Entity\Artiste;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * Faker Generator
     *
     * @var Generator
     */
    private Generator $faker;

    /**
     * Classe qui hash le pswd
     *
     * @var UserPasswordHasherInterface
     */
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->faker = Factory::create('fr_FR');
        $this->userPasswordHasher = $userPasswordHasher;

    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        //Authentified User
        for ($i = 1; $i <= 10; $i++) {
            $userUser = new User();
            $password = $this->faker->password(2,6);
            $userUser->setUsername($this->faker->userName() . '@' . $password)
            ->setRoles(["ROLE_USER"])
            ->setPassword($this->userPasswordHasher->hashPassword($userUser, $password));
            $manager->persist($userUser);
        }
        //Authentified Admin
        $adminUser = new User();
        $password = 'password';
        $adminUser->setUsername("admin")
        ->setRoles(["ROLE_ADMIN"])
        ->setPassword($this->userPasswordHasher->hashPassword($adminUser, $password));
        $manager->persist($adminUser);

        $artistList = [];
        $placeList = [];
        $artist = new Artiste();
        $artist->setArtistName("ZKR")->setArtistCategory("Rap")->setStatus(true);
        $artistList[]= $artist;
        $manager->persist($artist);



        for ($i = 1; $i <= 10; $i++) {

            $place = new Place();
            $place->setPlaceName($this->faker->realText($maxNbChars = 20, $indexSize = 1))
                ->setPlaceAddress($this->faker->sentence())
                ->setPlaceRegion($this->faker->departmentNumber())
                ->setStatus(true);
            $placeList[]=$place;
            $manager->persist($place);

            $event = new Event();
            $event->setEventName($this->faker->realText($maxNbChars = 20, $indexSize = 1))
                ->setEventDate($this->faker->dateTimeBetween('now', '+6 months'))
                ->setArtist($artistList[array_rand($artistList)])
                ->setPlace($placeList[array_rand($placeList)])
                ->setStatus(true);
            $manager->persist($event);

        }

        $manager->flush();
    }
}
