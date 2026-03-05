<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CartFillingService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param Cart $cart The cart to add the cartItem to.
     * @param Product $product The product to associate the cartItem with.
     */
    public function persistCartItem(Cart $cart, Product $product): void {
        $cartItem = new CartItem();
        $cartItem->setProduct($product);
        $cartItem->setQuantity(1);

        $cart->addCartItem($cartItem);

        $this->entityManager->persist($cartItem);
        $this->entityManager->flush();
    }

    /**
     * An omni-method for addQuantity & persistCartItem
     * This will add the quantity if a cartItem is already in the cart, otherwise persist a cartItem. This is a mix of both parameters from these 2 methods to satisfy them.
     */
    public function addQuantityOrPersistCartItem(Cart $cart, Collection $cartItems, Product $product): void {
        if (!$this->addQuantity($cartItems, $product)) {
            $this->persistCartItem($cart, $product);
        }
    }

    /**
     * @param Collection $cartItems To loop through them.
     * @param Product $product To check if the cartItems's product matches the product that needs to be added.
     * @return bool Returns true if the quantity has been added, meaning the product was already in the cart, otherwise false.
     */
    public function addQuantity(Collection $cartItems, Product $product): bool {
        foreach ($cartItems as $cartItem) {
            if ($cartItem->getProduct()->getId() === $product->getId()) {
                $cartItem->setQuantity($cartItem->getQuantity() + 1);

                $this->entityManager->flush();

                return true;
            }
        }

        return false;
    }
}
