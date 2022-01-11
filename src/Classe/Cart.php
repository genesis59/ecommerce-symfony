<?php

namespace App\Classe;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    private $session;
    private $productRepo;

    public function __construct(RequestStack $stack, ProductRepository $productRepo)
    {
        $this->session = $stack->getSession();
        $this->productRepo = $productRepo;
    }

    public function add($id)
    {
        $cart = $this->get();

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
        $this->session->set('cart', $cart);
    }

    public function decrease($id)
    {
        $cart = $this->get();

        if ($cart[$id] <= 1) {
            unset($cart[$id]);
        } else {
            $cart[$id]--;
        }
        $this->session->set('cart', $cart);
    }

    public function remove()
    {
        $this->session->remove('cart');
    }

    public function get()
    {
        return $this->session->get('cart', []);
    }

    public function delete($id)
    {
        $cart = $this->get();
        unset($cart[$id]);
        $this->session->set('cart', $cart);
    }

    public function getFull()
    {
        $cartComplete = [];
        foreach ($this->get() as $id => $quantity) {
            $product = $this->productRepo->findOneById($id);
            if(!$product) {
                $this->delete($id);
                continue;
            }
            $cartComplete[] = [
                'product' => $product,
                'quantity' => $quantity
            ];
        }
        return $cartComplete;
    }
}
