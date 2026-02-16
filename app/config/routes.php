<?php

use app\controllers\DashboardController;
use app\controllers\BesoinController;
use app\controllers\DonController;
use app\controllers\DispatchController;
use app\controllers\VilleController;
use flight\Engine;
use flight\net\Router;

/** 
 * @var Router $router 
 * @var Engine $app
 */

$router->get('/', [DashboardController::class, 'index']);

$router->group('/besoins', function(Router $router) {
    $router->get('', [BesoinController::class, 'index']);
    $router->post('/store', [BesoinController::class, 'store']);
});

$router->group('/dons', function(Router $router) {
    $router->get('', [DonController::class, 'index']);
    $router->post('/store', [DonController::class, 'store']);
});

$router->group('/dispatch', function(Router $router) {
    $router->get('', [DispatchController::class, 'index']);
    $router->post('/run', [DispatchController::class, 'run']);
});

$router->group('/villes', function(Router $router) {
    $router->get('', [VilleController::class, 'index']);
    $router->post('/store', [VilleController::class, 'store']);
    $router->post('/delete', [VilleController::class, 'delete']);
    $router->post('/region/store', [VilleController::class, 'storeRegion']);
});
