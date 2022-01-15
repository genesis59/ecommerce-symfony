<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Product;
use App\Form\SearchType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $productRepo;

    public function __construct(ProductRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    #[Route('/nos-produits', name: 'products')]
    public function index(Request $request): Response
    {
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $products = $this->productRepo->findWithSearch($search);
        } else {
            $products = $this->productRepo->findAll();
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView()
        ]);
    }

    #[Route('/produit/{slug}', name: 'product')]
    public function show($slug): Response
    {

        $product = $this->productRepo->findOneBy(['slug' => $slug]);
        $products = $this->productRepo->findBy(['isBest' => 1]);
        if (!$product) {
            return $this->redirectToRoute('products');
        }
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'products' => $products
        ]);
    }
}
