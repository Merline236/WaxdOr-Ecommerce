<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Products;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductsFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger){}
    
    
    public function load(ObjectManager $manager): void
    {
        // On utilise FAKER pour avoir des fixtures en francais
        $faker=Factory::create('fr_FR');

         // On fait une boucle pour creer plusieurs utilisateurs
         for($prod = 1; $prod <=10; $prod++){
            $product = new Products();
            $product->setName($faker->text(15));
            $product->setDescription($faker->text());
            $product->setSlug($this->slugger->slug($product->getName())->lower());
            $product->setPrice($faker->numberBetween(900, 150000));
            $product->setStock($faker->numberBetween(0, 10));
            
            // On va chercher une reference de catégorie
            $category = $this->getReference('cat-'.rand(1, 8));
            $product->setCategories($category);
           
            $manager->persist($product);
            // je stocke ma référence qui me permettra dans Product d'aller chercher la ref
            $this->setReference('prod-'.$prod, $product);
         }

        $manager->flush();
    }
}
