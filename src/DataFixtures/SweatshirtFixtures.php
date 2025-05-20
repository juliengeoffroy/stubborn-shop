<?php

namespace App\DataFixtures;

use App\Entity\Sweatshirt;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SweatshirtFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $sweats = [
            ['Blackbelt', 29.90, 'blackbelt.jpg'],
            ['BlueBelt', 29.90, 'bluebelt.jpg'],
            ['Street', 34.50, 'street.jpg'],
            ['Pokeball', 45.00, 'pokeball.jpg'],
            ['PinkLady', 29.90, 'pinklady.jpg'],
            ['Snow', 32.00, 'snow.jpg'],
            ['Greyback', 28.50, 'greyback.jpg'],
            ['BlueCloud', 45.00, 'bluecloud.jpg'],
            ['Borninusa', 59.90, 'borninusa.jpg'], 
            ['GreenSchool', 42.20, 'greenschool.jpg'],
        ];

        foreach ($sweats as [$name, $price, $image]) {
            $sweat = new Sweatshirt();
            $sweat->setName($name)
                ->setPrice($price)
                ->setImage($image)
                ->setIsFeatured(
                    str_contains($name, 'Blackbelt') ||
                    str_contains($name, 'Borninusa') ||
                    str_contains($name, 'Pokeball')
                );
            $sweat->setStockXS(2);
            $sweat->setStockS(2);
            $sweat->setStockM(2);
            $sweat->setStockL(2);
            $sweat->setStockXL(2);    
            $manager->persist($sweat);
        }
        $manager->flush();
    }
}
