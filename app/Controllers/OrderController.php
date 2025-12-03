<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Request;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\ProductService;

class OrderController extends BaseController
{
    public OrderService $orders;
    public ProductService $products;
    private Request $request;
    private CartService $cartService;

    public function __construct(OrderService $orders, ProductService $productService, CartService $cartService, Request $request)
    {
        parent::__construct();
        $this->orders = $orders;
        $this->products = $productService;
        $this->cartService = $cartService;
        $this->request = $request;
    }

    public function index(): void
    {
        $this->render('pages/order', ['title' => 'Order']);
    }

    public function checkout(): void
    {
        $this->render('pages/checkout', ['title' => 'Checkout']);
    }

    public function placeOrder(): void
    {
        try {
            // Extract form fields
            $data = [
                'customer_email'       => $this->request->post('customer_email'),
                'customer_name'        => $this->request->post('customer_name'),
                'shipping_address'     => $this->request->post('shipping_address'),
                'shipping_city'        => $this->request->post('shipping_city'),
                'shipping_country'     => $this->request->post('shipping_country'),
                'shipping_postal_code' => $this->request->post('shipping_postal_code'),
                'phone'                => $this->request->post('phone'),
                'payment_method'       => $this->request->post('payment_method'),
            ];

            // Logged-in user OR guest
            $userId = $_SESSION['user_id'] ?? null;
            $sessionId = session_id();

            // Get cart items with products (CartItem objects with product data)
            $items = $this->cartService->getCartWithProducts($userId, $sessionId);

            if (empty($items)) {
                $this->json(['success' => false, 'message' => 'Your cart is empty']);
                return;
            }

            // Create order - pass CartItem objects directly
            $orderId = $this->orders->createOrder($userId, $data, $items);

            if (!$orderId) {
                $this->json(['success' => false, 'message' => 'Failed to create order'], 500);
                return;
            }

            // Clear cart after successful order
            $this->cartService->clearCart($userId, $sessionId);

            // Final success response
            $this->json([
                'success'  => true,
                'message'  => 'Order placed successfully',
                'order_id' => $orderId
            ]);

        } catch (\Exception $e) {
            logMessage('Checkout error: ' . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Checkout failed: ' . $e->getMessage()], 500);
        }
    }

    public function showOrder($orderId)
    {
        try {
            $orderDetails = $this->orders->getOrderDetails($orderId);
            $this->render('pages/order_show', ['title' => 'Order Details', 'body_class' => 'h-full', 'html_class' => 'h-full bg-gray-100', 'header' => true, 'is_admin' => true, 'admin_nav_active' => 'active', 'admin_footer_active' => 'active', 'order' => $orderDetails]);
        } catch (\Exception $e) {
            logMessage('Error rendering order show page: ' . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Failed to show order details'], 500);
        }
    }
}