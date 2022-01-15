<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/commande/merci/{stripeSessionId}', name: 'order_success')]
    public function index(Cart $cart, $stripeSessionId): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneBy(['stripeSessionId' => $stripeSessionId]);
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }
        $cart->remove();
        if ($order->getState() == 0) {
            $order->setState(1);
            $this->em->flush();

            $mail = new Mail();
            $content = "Merci pour votre commande";
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), "Commande réussie", $content);
        }
        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
