<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Users;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        private SluggerInterface $slugger
        
        ){}


    public function load(ObjectManager $manager): void
    {
         $admin = new Users();
         $admin->setEmail('ngaimerline@gmail.com');
         $admin->setLastname('NGAIDOMA');
         $admin->setFirstname('Merline');
         $admin->setAddress('12 Avenue du Bonheur');
         $admin->setZipcode('75008');
         $admin->setCity('Paris');
         $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin, 'admin')
        );
        $admin->setRoles(['ROLE_ADMIN']);
         
        $manager->persist($admin);

        //on utilise FAKER pour avoir des données en Français
        $faker= Factory::create('fr_FR');
        

        //on boucle pour créer plusieurs utilisateurs
        for($usr = 1; $usr <=5; $usr++) {
            $user = new Users();
            $user->setEmail($faker->email);
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastName);
            $user->setAddress($faker->streetAddress);
            //pour remplacer les espaces dans la génèration de code postaux
            $user->setZipcode(str_replace(' ', '', $faker->postcode));
            $user->setCity($faker->city);
            $user->setPassword(
            $this->passwordEncoder->hashPassword($user, 'user')
        );
        //on fait un dump pour voir ce que contient notre objet user afin de comprendre notre erreur
        //dump($user);
        
    
         
        $manager->persist($user);
        

    }

        $manager->flush();
    }
}
