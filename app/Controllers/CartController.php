<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Request;
use App\Services\CartService;

class CartController extends BaseController
{
    private Request $request;
    public function __construct(private readonly CartService $cart, Request $request) {
        parent::__construct();
        $this->request = $request;
    }

    public function index(): void
    {
        $this->render('pages/cart', ['title' => 'Cart']);
    }

    public function add(): void
    {
        $productId = (int)$this->request->post('product_id');
        $qty = (int)$this->request->post('qty') ?: 1;

        $this->cart->addToCart($productId, $qty);

        logMessage('Adding product to cart: Product ID: ' . $productId);

        $this->json([
            'success' => true,
            'message' => 'Product added to cart successfully'
        ]);
    }

    public function remove(): void
    {
        $id = (int)$this->request->post('id');
        $this->cart->remove($id);

        $this->json(['success' => true]);
    }

    public function update(): void
    {
        $id = (int)$this->request->post('id');
        $qty = (int)$this->request->post('qty');

        $this->cart->update($id, $qty);

        $this->json(['success' => true]);
    }

    public function show(): void
    {
        $items = $this->cart->getCart();
        $this->render('cart/show', ['items' => $items]);
    }
}
