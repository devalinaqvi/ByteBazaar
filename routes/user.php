<?php

// Dashboard
$router->get('/user/dashboard', 'UserController@dashboard', 'protected_dashboard');
$router->get('/user/order/[i:id]', 'OrderController@showOrder', 'protected_order');