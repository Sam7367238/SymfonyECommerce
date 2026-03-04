<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/product", name: "product_")]
final class ProductController extends AbstractController
{
    public function __construct(private readonly ProductRepository $repository)
    {
    }

    #[Route('/', name: 'index')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $query = $this->repository->createQueryBuilder('p');

        $products = $paginator->paginate(
            $query,
            $request->query->getInt("page", 1),
            15
        );

        return $this->render('product/index.html.twig', compact("products"));
    }
}
