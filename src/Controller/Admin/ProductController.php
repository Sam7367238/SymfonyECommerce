<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\FileUploaderService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/admin/product", name: "admin_product_")]
final class ProductController extends AbstractController
{
    public function __construct(private readonly ProductRepository $repository)
    {
    }

    #[Route('/', name: 'index')]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $query = $this->repository->createQueryBuilder('p');

        $products = $paginator->paginate(
            $query,
            $request->query->getInt("page", 1),
            15
        );

        return $this->render('admin/product/index.html.twig', compact("products"));
    }

    #[Route("/new", name: "new")]
    public function new(Request $request, FileUploaderService $fileUploaderService): Response {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        return $this->render("admin/product/new.html.twig", compact("form"));
    }
}
