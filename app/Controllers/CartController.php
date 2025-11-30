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
        $cartItems = $this->cart->getCart();
        $processedItems = [];
        $cartTotal = 0;

        foreach ($cartItems as $item) {
            // Access object properties with -> instead of []
            $price = $this->cart->getItemPrice($item->product_id);
            $image = $this->cart->getProductImage($item->product_id);
            $name = $this->cart->getProductName($item->product_id);
            $subtotal = $item->quantity * $price;
            $cartTotal += $subtotal;

            $processedItems[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $price,
                'subtotal' => $subtotal,
                'image' => $image,
                'product_name' => $name
            ];
        }

        $this->render('pages/cart', [
            'title' => 'Cart',
            'cartItems' => $processedItems,
            'cartTotal' => $cartTotal
        ]);
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
