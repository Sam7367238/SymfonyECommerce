<?php

namespace App\DataFixtures;

use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function loadUsers(ObjectManager $manager): static {
        $cart = new Cart();
        $user = new User();
        $user->setEmail("dummy@email.com");
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, "DummyPassword")
        );
        $user->setRoles(["ROLE_USER"]);
        $cart->setUser($user);
        $manager->persist($user);
        $manager->persist($cart);

        $cart = new Cart();
        $user = new User();
        $user->setEmail("admin@email.com");
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, "AdminPassword")
        );
        $user->setRoles(["ROLE_ADMIN"]);
        $cart->setUser($user);
        $manager->persist($user);
        $manager->persist($cart);

        return $this;
    }

    public function loadProducts(ObjectManager $manager): static {
        for ($i = 0; $i < 10; $i++) {
            $product = new Product();
            $product->setName("Product $i");
            $product->setImagePath("None");
            $product->setPrice(10.00);
            $manager->persist($product);
        }

        return $this;
    }

    public function load(ObjectManager $manager): void
    {
        $this
        ->loadProducts($manager)
        ->loadUsers($manager);

        $manager->flush();
    }
}
