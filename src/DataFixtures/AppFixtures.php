<?php

namespace App\DataFixtures;

use App\Factory\AddressFactory;
use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne(["name" => "Vergueiro", "firstname" => "Steven", "password" => "steven", "email" => "steven@gigakebab.fr"]);
        UserFactory::createOne(["name" => "Martin", "firstname" => "Jeandoline", "password" => "jeandoline", "email" => "jeandoline@gigakebab.fr"]);
        UserFactory::createOne(["name" => "Fernandez", "firstname" => "alexandre", "password" => "alexandre", "email" => "alexandre@gigakebab.fr"]);
        UserFactory::createOne(["name" => "Messaoudene", "firstname" => "Tim", "password" => "tim", "email" => "tim@gigakebab.fr"]);
        UserFactory::createMany(50);

        ProductFactory::createOne(["name" => "Le Classique", "description" => "Toi même tu sais", "price" => 4.99]);
        ProductFactory::createOne(["name" => "Le Giaga Tim", "description" => "Kebab couscous bien mouillé supplément pois chiche", "price" => 4.99]);
        ProductFactory::createOne(["name" => "La Gigadoline", "description" => "Le classique avec deux tomates et une merguez", "price" => 4.99]);
        ProductFactory::createOne(["name" => "La galette d'Alexandre", "description" => "Le classique dans sa galette maison avec sa sauce blanche crémeuse", "price" => 4.99]);
        ProductFactory::createOne(["name" => "L'assiette gourmande", "description" => "L'assiette avec Giga saussice pour les Giga gourmands", "price" => 4.99]);
        ProductFactory::createOne(["name" => "Le Giga Americain Steven", "description" => "L'américain mourue poilue", "price" => 4.99]);

        AddressFactory::createMany(100, function() {
            return [
                'user' => UserFactory::random(),
            ];
        });
        $manager->flush();
    }
}
