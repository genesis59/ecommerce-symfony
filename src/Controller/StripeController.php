<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{

    #[Route('/commande/create-session/{reference}', name: 'stripe_create_session')]
    public function index($reference, EntityManagerInterface $em) : Response
    {
        $order = $em->getRepository(Order::class)->findOneBy(['reference' => $reference]);
        if(!$order){
            //TODO message erreur
            return $this->redirectToRoute('home');
        }
        $product_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8001';
        Stripe::setApiKey($_ENV['API_KEY_STRIPE']);

        foreach ($order->getOrderDetails()->getValues() as $product) {
            $product_for_stripe[] = [
                'price' => Price::create([
                    'product' => Product::create([
                        'name' => $product->getProduct()
                    ]),
                    'unit_amount' => $product->getPrice(),
                    'currency' => 'eur',
                ]),
                'quantity' => $product->getQuantity(),
            ];
        }
        $product_for_stripe[] = [
            'price' => Price::create([
                'product' => Product::create([
                    'name' => 'Livraison: '.$order->getCarrierName()
                ]),
                'unit_amount' => $order->getCarrierPrice(),
                'currency' => 'eur',
            ]),
            'quantity' => 1,
        ];

        $checkout_session = Session::create([
            'line_items' => [
                $product_for_stripe
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);
        $em->flush();

        return $this->redirect($checkout_session->url,303);
    }
}
