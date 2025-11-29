<?php
// routes/web.php: User-facing routes (public, protected, guest)

// Public routes
$router->get('/', 'HomeController@index', 'public_home');
$router->get('/products', 'ProductController@index', 'public_products');
$router->get('/product/[i:id]', 'ProductController@show', 'public_product_detail');

// Guest-only (login/register—no auth needed)
$router->get('/login', 'AuthController@login', 'guest_login');
$router->post('/login', 'AuthController@authenticate', 'guest_login_post');
$router->get('/register', 'AuthController@register', 'guest_register');
$router->post('/register', 'AuthController@create', 'guest_register_post');
$router->get('/logout', 'AuthController@logout', 'public_logout');

// Protected routes (auth required: cart, orders)
$router->get('/cart', 'CartController@index', 'protected_cart');
$router->post('/cart/add/[i:productId]', 'CartController#add', 'protected_cart_add');
$router->post('/cart/update', 'CartController@update', 'protected_cart_update');
$router->post('/cart/remove/[i:itemId]', 'CartController@remove', 'protected_cart_remove');
$router->get('/checkout', 'OrderController@checkout', 'protected_checkout');
$router->post('/checkout', 'OrderController@placeOrder', 'protected_place_order');
$router->get('/orders', 'OrderController@history', 'protected_orders');
?>