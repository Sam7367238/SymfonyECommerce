<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CartItemRepository;
use App\Service\CartFillingService;
use App\Service\CartRemovalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;

#[Route(name: 'cart_')]
final class CartController extends AbstractController
{
    public function __construct(
        private readonly CartItemRepository $cartItemRepository,
        private readonly CartFillingService $cartFillingService,
        private readonly CartRemovalService $cartRemovalService,
    ) {
    }

    #[Route('/cart', name: 'index')]
    public function index(#[CurrentUser] User $user)
    {
        $cart = $user->getCart();
        $cartItems = $this->cartItemRepository->findByCartIdJoinedToProduct($cart->getId());

        return $this->render('cart/index.html.twig', compact('cartItems'));
    }

    #[IsCsrfTokenValid(new Expression("'cartItem-remove-' ~ args['cartItem'].getId()"), tokenKey: 'token')]
    #[Route('/cart/remove-product/{id}', name: 'remove', methods: ['POST'])]
    public function removeFromCart(#[CurrentUser] User $user, CartItem $cartItem): Response
    {
        $cart = $user->getCart();

        $this->cartRemovalService->decreaseQuantityOrRemoveCartItem($cart, $cartItem);

        $this->addFlash('status', 'Product Has Been Removed From Cart');

        return $this->redirectToRoute('cart_index');
    }

    #[IsCsrfTokenValid(new Expression("'product-add-to-cart-' ~ args['product'].getId()"), tokenKey: 'token')]
    #[Route('/product/{id}/cart', name: 'add', methods: ['POST'])]
    public function addToCart(#[CurrentUser] User $user, Product $product): Response
    {
        $cart = $user->getCart();
        $cartItems = $cart->getCartItems();

        $this->cartFillingService->addQuantityOrPersistCartItem($cart, $cartItems, $product);

        $this->addFlash('status', 'Product Added To Cart');

        return $this->redirectToRoute('product_show', ['id' => $product->getId()]);
    }
}
