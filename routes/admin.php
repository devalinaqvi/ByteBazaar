<?php
// routes/admin.php: Admin-only routes (protected by AdminMiddleware)

// Dashboard
$router->get('/admin', 'AdminController@index', 'admin_dashboard');

// Products CRUD
$router->get('/admin/products', 'AdminController@products', 'admin_products');
$router->post('/admin/products', 'AdminController@store', 'admin_store_product');
$router->get('/admin/products/[i:id]/edit', 'AdminController@editProduct', 'admin_edit_product');
$router->post('/admin/products/[i:id]', 'AdminController@update', 'admin_update_product');
$router->post('/admin/products/[i:id]/delete', 'AdminController@destroy', 'admin_delete_product');

// Orders
$router->get('/admin/orders', 'AdminController@orders', 'admin_orders');
$router->get('/admin/orders/[i:id]', 'AdminController@showOrder', 'admin_order_detail');
$router->post('/admin/orders/[i:id]/update-status', 'AdminController@updateStatus', 'admin_update_order_status');
?>