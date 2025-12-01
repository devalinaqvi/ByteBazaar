<?php
// routes/admin.php: Admin-only routes (protected by AdminMiddleware)

// Dashboard
$router->get('/admin', 'AdminController@index', 'admin_dashboard');

// Products CRUD
$router->get('/admin/products', 'AdminController@products', 'admin_products');
$router->get('/admin/products/create', 'AdminController@createProduct', 'admin_create_product');
$router->post('/admin/products/create', 'AdminController@storeProduct', 'admin_store_product');
$router->get('/admin/products/[i:id]/edit', 'AdminController@editProduct', 'admin_edit_product');
$router->post('/admin/products/[i:id]', 'AdminController@updateProduct', 'admin_update_product');
$router->post('/admin/products/[i:id]/delete', 'AdminController@deleteProduct', 'admin_delete_product');
$router->get('/admin/users', 'AdminController@viewUsers', 'admin_view_user');

// Categories CRUD
$router->get('/admin/categories', 'CategoryController@index', 'admin_categories');
$router->get('/admin/categories/create', 'CategoryController@create', 'admin_create_category');
$router->post('/admin/categories/create', 'CategoryController@store', 'admin_store_category');
$router->get('/admin/categories/[i:id]/edit', 'CategoryController@edit', 'admin_edit_category');
$router->post('/admin/categories/[i:id]', 'CategoryController@update', 'admin_update_category');
$router->post('/admin/categories/[i:id]/delete', 'CategoryController@delete', 'admin_delete_category');

// Orders
$router->get('/admin/orders', 'AdminController@orders', 'admin_orders');
$router->get('/admin/order/[i:id]', 'OrderController@showOrder', 'admin_order_detail');
$router->post('/admin/order/[i:id]/update-status', 'OrderController@updateStatus', 'admin_update_order_status');
?>