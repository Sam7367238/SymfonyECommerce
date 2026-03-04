<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;

final class CartController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CartItemRepository $cartItemRepository
    )
    {
    }

    #[Route("/cart", name: "cart_index")]
    public function index(#[CurrentUser] User $user)
    {
        $cart = $user->getCart();
        $cartItems = $this->cartItemRepository->findByCartIdJoinedToProduct($cart->getId());

        return $this->render("cart/index.html.twig", compact("cartItems"));
    }

    #[IsCsrfTokenValid(new Expression("'product-add-to-cart-' ~ args['product'].getId()"), tokenKey: "token")]
    #[Route('/product/{id}/cart', name: 'cart_add', methods: ["POST"])]
    public function addToCart(#[CurrentUser] User $user, Product $product): Response
    {
        $cartItem = new CartItem();
        $cartItem->setProduct($product);

        $user->getCart()->addCartItem($cartItem);

        $this->entityManager->persist($cartItem);
        $this->entityManager->flush();

        $this->addFlash("status", "Product Added To Cart");
        return $this->redirectToRoute("product_show", ["id" => $product->getId()]);
    }
}
