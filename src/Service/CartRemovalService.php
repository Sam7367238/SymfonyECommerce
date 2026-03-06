<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CartRemovalService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param Cart $cart The cart to remove the cartItem from.
     * @param CartItem $cartItem The cartItem to be wiped from the database.
     */
    public function removeCartItem(Cart $cart, CartItem $cartItem): void {

        $cart->removeCartItem($cartItem);

        $this->entityManager->remove($cartItem);
        $this->entityManager->flush();
    }

    /**
     * An omni-method which decides to remove the cartItem or decreasing the quantity.
     * When the decrease quantity method returns false, it means there is only 1 quantity of that cartItem, in which case the removeCartItem method is called.
     * The parameters are a mix of both parameters required in the 2 methods to satisfy their requirements.
     */
    public function decreaseQuantityOrRemoveCartItem(Cart $cart, CartItem $cartItem): void {
        if (!$this->decreaseQuantity($cartItem)) {
            $this->removeCartItem($cart, $cartItem);
        }
    }

    /**
     * @return bool Returns true if there's more than 1 to decrease the quantity, returns false if there was only 1 product in the cart, which signals it should be removed from the cart.
     */
    public function decreaseQuantity(CartItem $cartItem): bool {
        $quantity = $cartItem->getQuantity();

        if ($quantity >= 2) {
            $cartItem->setQuantity($quantity - 1);
            $this->entityManager->flush();

            return true;
        }

        return false;
    }
}
