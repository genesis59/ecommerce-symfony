<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountOrderController extends AbstractController
{

    #[Route('/compte/mes-commandes', name: 'account_order')]
    public function index(OrderRepository $orderRepo): Response
    {
        $orders = $orderRepo->findSuccessOrders($this->getUser());
        return $this->render('account/order.html.twig',[
            'orders' => $orders
        ]);
    }

    #[Route('/compte/mes-commandes/{reference}', name: 'account_order_show')]
    public function show(OrderRepository $orderRepo,$reference): Response
    {
        $order = $orderRepo->findOneBy(['reference' => $reference]);
        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('account_order');
        }
        return $this->render('account/order_show.html.twig',[
            'order' => $order
        ]);
    }
}
