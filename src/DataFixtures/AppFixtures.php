<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Invoice;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class AppFixtures extends Fixture
{
    /**
     * Encodeur de mot de passe
     * @var UserPasswordHasherInterface
     */
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder){
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');

        // On va créer 10 users fictifs
        for($u = 0; $u < 10; $u++){
            $user = new User();

            // On reinitialise les numeros de facture pour chaque nouvel user
            $chrono = 1;
            // password hashé du user
            $hash = $this->encoder->hashPassword($user, "password");

            $user->setFirstName($faker->firstName())
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setPassword($hash)
            ;
            $manager->persist($user);

            // On va créer entre 5 et 20 clients fictifs rattachés à un user
            for($c = 0; $c < mt_rand(5,20); $c++){
                $customer = new Customer();
                $customer->setFirstName($faker->firstName())
                    ->setLastName($faker->lastName)
                    ->setEmail($faker->email)
                    ->setCompany($faker->company)
                    ->setUtilisateur($user)
                ;
                $manager->persist($customer);

                /** Chaque client aura entre 3 et 10 factures
                 * chaque facture pourrait avoir 3 Etats: SENT, PAID, CANCELLED.
                 * */
                for($i = 0; $i < mt_rand(3,10); $i++) {
                    $invoice = new Invoice();
                    $invoice->setAmout($faker->randomFloat(2, 250, 5000))
                        ->setSentAt($faker->dateTimeBetween('-6 months'))
                        ->setStatus($faker->randomElement(['SENT', 'PAID', 'CANCELLED']))
                        ->setCustomer($customer)
                        ->setChrono($chrono)
                    ;
                    $chrono++;
                    $manager->persist($invoice);
                }
            }
        }
        $manager->flush();
    }
}
