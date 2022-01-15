<?php

namespace App\Controller;

use App\Entity\Header;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    public function __construct(private EntityManagerInterface $em)
    {
        
    }
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $products = $this->em->getRepository(Product::class)->findBy(['isBest' => 1]);
        $headers = $this->em->getRepository(Header::class)->findAll();
        return $this->render('home/index.html.twig',[
            'products' => $products,
            'headers' => $headers
        ]);
    }
}
