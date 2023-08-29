<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixtures extends Fixture

{//Je crée un compteur pour les catégories de produits
    private $counter = 1;

    //si je ne veux pas avoir à répéter le SLUGG dans mes fixtures j'utilise une fonction
    public function __construct(private SluggerInterface $slugger){}
    
    public function load(ObjectManager $manager): void
    {
        $parent = $this->createCategories('FEMME', null, $manager);
        $this->createCategories('Robe', $parent, $manager);
        $this->createCategories('Ensemble-Femme', $parent, $manager);
        
    
        $parent = $this->CreateCategories('HOMME', null, $manager);
        $this->createCategories('Chemise', $parent, $manager);
        $this->createCategories('Ensemble-Homme', $parent, $manager);
        

        $parent = $this->CreateCategories('ACCESSOIRES', null, $manager);
        $this->createCategories('Sac', $parent, $manager);
        $this->createCategories('Coque', $parent, $manager);
        $this->createCategories('Mug', $parent, $manager);
    
    
    
        $manager->flush();
    
        }
    
    
        //On autorise la création de Categories
        public function CreateCategories(string $name, Categories $parent= null, ObjectManager $manager)
        {
            $category = new Categories();
            $category ->setName($name);
            $category->setSlug($this->slugger->slug($category->getName())->lower());
            $category->setParent($parent);
            $manager->persist($category);
    
    
            //je stock le numéro de chaque catégories
            $this->addReference('cat-' .$this->counter, $category);
            //j'incrémente
            $this->counter++;
            
            return $category; //pour récupérer le parent
    }
}
