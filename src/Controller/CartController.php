<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;

final class CartController extends AbstractController
{
    #[IsCsrfTokenValid(new Expression("'product-add-to-cart-' ~ args['product'].getId()"), tokenKey: "token")]
    #[Route('/product/{id}/cart', name: 'cart_add', methods: ["POST"])]
    public function index(Product $product): Response
    {
        dd("Added to cart!", $product);
    }
}
