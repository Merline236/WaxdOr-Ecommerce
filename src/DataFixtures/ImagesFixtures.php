<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Images;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use faker;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

//Pour ordonner nos DATAFIXTURES, on utilise le implements DependentFixturesInterface
// class ImagesFixtures extends Fixture
class ImagesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // On utilise FAKER pour avoir des fixtures en francais
        $faker=Factory::create('fr_FR');

        for($img =1; $img <= 100; $img++){
            $image = new Images();
            $image->setName($faker->image(null, 640, 480));
            // On va chercher une reference de produit
            $product = $this->getReference('prod-'.rand(1, 10));
            $image->setProducts($product);
            $manager->persist($image);

        }

        $manager->flush();
    }

    // Comme on a utilisé la methode IndependFixtureInterface, on doit l'utiliser. 
    // Dans le tableau on stock la fixture qui doit etre éxecuter avant ImagesFixtures
    public function getDependencies():array
    {
        return[
            ProductsFixtures::class
        ];
    }
}
