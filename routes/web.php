<?php
// routes/web.php: User-facing routes (public, protected, guest)

// Public routes
$router->get('/', 'HomeController@index', 'public_home');
$router->get('/products', 'ProductController@index', 'public_products');
$router->get('/product/[*:id]', 'ProductController@show', 'public_product_detail');

// Guest-only (login/register—no auth needed)
$router->get('/login', 'AuthController@login', 'guest_login');
$router->post('/login', 'AuthController@authenticate', 'guest_login_post');
$router->get('/register', 'AuthController@register', 'guest_register');
$router->post('/register', 'AuthController@postRegister', 'guest_register_post');
$router->get('/logout', 'AuthController@logout', 'public_logout');

// Protected routes (auth required: cart, orders)
$router->get('/cart', 'CartController@index', 'cart');
$router->post('/cart/add/[i:productId]', 'CartController@add', 'cart_add');
$router->post('/cart/update', 'CartController@update', 'cart_update');
$router->post('/cart/remove/[i:itemId]', 'CartController@remove', 'cart_remove');
$router->get('/checkout', 'OrderController@checkout', 'checkout');
$router->post('/checkout', 'OrderController@placeOrder', 'checkout_post');
$router->get('/orders', 'OrderController@history', 'protected_orders');
$router->get('/account', 'AccountController@index', 'protected_account');

$router->get('/api/cart', 'CartController@api_index', 'api_cart');

?>