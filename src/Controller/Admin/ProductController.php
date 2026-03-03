<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\FileUploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route("/admin/product", name: "admin_product_")]
final class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $repository,
        private readonly EntityManagerInterface $entityManager
    )
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
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get("imagePath")->getData();

            $imageFileName = $fileUploaderService->upload($file);
            $product->setImagePath($imageFileName);

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            $this->addFlash("status", "Product Created Successfully");
            return $this->redirectToRoute("admin_product_index");
        }

        return $this->render("admin/product/new.html.twig", compact("form"));
    }

    #[Route("/{id}/edit", name: "edit")]
    public function edit(Request $request, FileUploaderService $fileUploaderService, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get("imagePath")->getData();

            $imageFileName = $fileUploaderService->upload($file);
            $product->setImagePath($imageFileName);

            $this->entityManager->flush();

            $this->addFlash("status", "Product Edited Successfully");
            return $this->redirectToRoute("admin_product_index");
        }

        return $this->render("admin/product/edit.html.twig", compact("form"));
    }

    #[Route("/{id}/delete", name: "delete")]
    public function delete(Request $request, Product $product): Response {
        if ($request->isMethod("POST")) {
            $this->entityManager->remove($product);
            $this->entityManager->flush();

            $this->addFlash("status", "Product Deleted Successfully");
            return $this->redirectToRoute("admin_product_index");
        }

        return $this->render("admin/product/delete.html.twig", compact("product"));
    }
}
