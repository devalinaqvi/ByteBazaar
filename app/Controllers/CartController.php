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
        $processedItems = array_map(fn($item) => $this->processCartItem($item), $cartItems);
        $cartTotal = array_sum(array_column($processedItems, 'subtotal'));

        $this->render('pages/cart', [
            'title' => 'Cart',
            'cartItems' => $processedItems,
            'cartTotal' => $cartTotal
        ]);
    }

    public function api_index(): void
    {
        $cartItems = $this->cart->getCart();
        $processedItems = array_map(fn($item) => $this->processCartItem($item), $cartItems);
        $this->json(['items' => $processedItems]);
    }

    private function processCartItem($item): array
    {
        $price = $this->cart->getItemPrice($item->product_id);
        $subtotal = $item->quantity * $price;

        return [
            'id' => $item->id,
            'product_id' => $item->product_id,
            'quantity' => $item->quantity,
            'price' => $price,
            'subtotal' => $subtotal,
            'image' => $this->cart->getProductImage($item->product_id),
            'product_name' => $this->cart->getProductName($item->product_id),
            'product_slug' => $this->cart->getProductSlug($item->product_id)
        ];
    }

    public function add(): void
    {
        try {
            $productId = (int)$this->request->post('product_id');
            $qty = (int)$this->request->post('qty') ?: 1;

            $this->cart->addToCart($productId, $qty);

            logMessage('Adding product to cart: Product ID: ' . $productId);

            $cart_count = $this->cart->count();
            $_SESSION['cart_count'] = $cart_count;
            logMessage('SESSION Cart count: ' . $cart_count);

            $this->json([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'cart_count' => $cart_count
            ]);
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function remove(): void
    {
        $id = (int)$this->request->post('id');
        logMessage('Removing item from cart: Item ID: ' . $id);
        $this->cart->remove($id);

        $cart_count = $this->cart->count();
        $_SESSION['cart_count'] = $cart_count;
        logMessage('SESSION Cart count: ' . $cart_count);

        $this->json([
            'success' => true,
            'cart_count' => $cart_count
        ]);
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
