<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');  // Home page (protected)
$routes->get('/login', 'Auth::login');  // Login page
$routes->post('/login', 'Auth::login');  // Handle login POST request
$routes->get('/logout', 'Auth::logout');  // Logout and redirect to login



$routes->get('inventory', 'InventoryController::index');
$routes->get('inventory/(:num)', 'InventoryController::sub_inventory_type/$1');
$routes->get('inventory/(:num)/(:num)', 'InventoryController::show/$1/$2');
$routes->post('/upload/doUpload', 'InventoryController::doUpload');
$routes->get('inventory/api/(:segment)', 'InventoryController::getInventoryById/$1');
$routes->post('inventory/api/store', 'InventoryController::store');
$routes->post('inventory/api/update/(:segment)', 'InventoryController::update/$1');
$routes->delete('inventory/api/delete/(:segment)', 'InventoryController::delete/$1');
$routes->get('inventory/export', 'InventoryController::export');
$routes->get('/inventory/list', 'InventoryController::getInventoryList');
$routes->get('/inventory/in/list', 'InventoryController::getInventoryInList');
$routes->post('/inventory/save-stock', 'InventoryController::saveStock');
$routes->post('/inventory/save-pos-stock', 'InventoryController::savePosStock');

$routes->post('inventory/save-out', 'InventoryController::saveOut');
$routes->post('inventory/save-return', 'InventoryController::saveReturn');

$routes->post('inventory/save-pos-out', 'InventoryController::savePosOut');

$routes->get('/inventory_in', 'InventoryController::inventoryIn');
$routes->get('/inventory_out', 'InventoryController::inventoryOut');
$routes->get('/inventory_return', 'InventoryController::inventoryReturn');
$routes->get('/inventory_return_history', 'InventoryController::inventoryReturnHistory');
$routes->get('/inventory_out_pos', 'InventoryController::inventoryOutPos');

$routes->get('api/inventory-history', 'InventoryHistoryController::getInventoryHistoryApi');
$routes->get('/inventory_history', 'InventoryHistoryController::index');


$routes->get('/inventory_history_filter', 'InventoryHistoryController::filter');

$routes->get('/inventory_excess', 'InventoryController::inventory_excess');

$routes->get('/supplier', 'SupplierController::index');
$routes->get('/supplier/list', 'SupplierController::getSupplierList');
$routes->get('/inventorysupplier/api/(:num)', 'SupplierController::api/$1');
$routes->post('/inventorysupplier/api/store', 'SupplierController::apiStore');
$routes->post('/inventorysupplier/api/update/(:num)', 'SupplierController::apiUpdate/$1');
$routes->delete('/inventorysupplier/api/delete/(:num)', 'SupplierController::apiDelete/$1');
$routes->get('inventorysupplier/export', 'SupplierController::export');


$routes->group('inventory/excess/api', function($routes) {
    $routes->get('(:num)', 'ExcessStockApi::show/$1');
    $routes->post('store', 'ExcessStockApi::create');
    $routes->post('update/(:num)', 'ExcessStockApi::update/$1');
    $routes->delete('delete/(:num)', 'ExcessStockApi::delete/$1');
});


$routes->get('notifications', 'NotificationController::index');         // GET all notifications
$routes->get('notifications/(:num)', 'NotificationController::show/$1'); // GET one notification by ID
$routes->post('notifications', 'NotificationController::create');        // POST create notification
$routes->put('notifications/(:num)', 'NotificationController::update/$1'); // PUT update notification by ID
$routes->patch('notifications/(:num)', 'NotificationController::update/$1'); // PATCH update notification
$routes->delete('notifications/(:num)', 'NotificationController::delete/$1'); // DELETE notification by ID
$routes->post('api/notifications/view/(:num)', 'NotificationController::markAsViewed/$1');


$routes->get('/notification_page', 'NotificationController::index_page');



$routes->get('/distributor', 'DistributorController::index');
$routes->get('/distributor/list', 'DistributorController::getDistributorList');
$routes->get('/inventorydistributor/api/(:num)', 'DistributorController::api/$1');
$routes->post('/inventorydistributor/api/store', 'DistributorController::apiStore');
$routes->post('/inventorydistributor/api/update/(:num)', 'DistributorController::apiUpdate/$1');
$routes->delete('/inventorydistributor/api/delete/(:num)', 'DistributorController::apiDelete/$1');
$routes->get('inventorydistributor/export', 'DistributorController::export');




$routes->get('/inventory_type', 'InventoryTypeController::index');
$routes->get('inventorytype/api/(:num)', 'InventoryTypeController::apiShow/$1');
$routes->post('inventorytype/api/store', 'InventoryTypeController::apiStore');
$routes->post('inventorytype/api/update/(:num)', 'InventoryTypeController::apiUpdate/$1');
$routes->delete('inventorytype/api/delete/(:num)', 'InventoryTypeController::apiDelete/$1');

$routes->get('inventorytype/export', 'InventoryTypeController::export');


$routes->get('/sub_inventory_type', 'SubInventoryTypeController::index');
$routes->get('subinventorytype/api/(:num)', 'SubInventoryTypeController::apiShow/$1');
$routes->post('subinventorytype/api/store', 'SubInventoryTypeController::apiStore');
$routes->post('subinventorytype/api/update/(:num)', 'SubInventoryTypeController::apiUpdate/$1');
$routes->delete('subinventorytype/api/delete/(:num)', 'SubInventoryTypeController::apiDelete/$1');
$routes->get('subinventorytype/export', 'SubInventoryTypeController::export');


$routes->get('profile', 'UserController::profile');
$routes->post('profile/update', 'UserController::update');
$routes->post('profile/uploadImage', 'UserController::uploadImage');
$routes->post('profile/changePassword', 'UserController::changePassword');
$routes->group('users', function($routes) {
    $routes->get('/', 'UserController::index');
    $routes->get('api/(:num)', 'UserController::api/$1');
    $routes->post('api/store', 'UserController::store');
    $routes->post('api/update/(:num)', 'UserController::update_user/$1');
    $routes->delete('api/delete/(:num)', 'UserController::delete/$1');
});

$routes->get('api/roles', 'UserController::getRoles');
