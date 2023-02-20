<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Invoice;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');

        // On va créer entre 5 et 20 clients fictifs rattachés à un user
        for($c = 0; $c < mt_rand(5,20); $c++) {
            $customer = new Customer();
            $customer->setFirstName($faker->firstName())
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setCompany($faker->company);
            $manager->persist($customer);

            /** Chaque client aura entre 3 et 10 factures
             * chaque facture pourrait avoir 3 Etats: SENT, PAID, CANCELLED.
             * */
            for ($i = 0; $i < mt_rand(3, 10); $i++) {
                $invoice = new Invoice();
                $invoice->setAmount($faker->randomFloat(2, 250, 5000))
                    ->setSentAt($faker->dateTimeBetween('-6 months'))
                    ->setStatus($faker->randomElement(['SENT', 'PAID', 'CANCELLED']))
                    ->setLeCustomer($customer);
                $manager->persist($invoice);
            }
        }
        $manager->flush();
    }
}
