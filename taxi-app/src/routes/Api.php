<?php

use AnnaBozzi\TaxiApp\Repositories\AccountRepository;
use AnnaBozzi\TaxiApp\Repositories\RideRepository;
use AnnaBozzi\TaxiApp\Services\AuthService;
use AnnaBozzi\TaxiApp\Services\FareCalculatorService;
use AnnaBozzi\TaxiApp\Middleware\AuthMiddleware;
use AnnaBozzi\TaxiApp\controllers\AccountController;
use AnnaBozzi\TaxiApp\controllers\RideController;
use AnnaBozzi\TaxiApp\controllers\PaymentController;
use Slim\Factory\AppFactory;

$pdo = require __DIR__ . '/../config/database.php';

$accountRepository = new AccountRepository($pdo);
$rideRepository = new RideRepository($pdo);

$authService = new AuthService();
$fareCalculator = new FareCalculatorService();

$accountController = new AccountController($accountRepository);
$rideController = new RideController($rideRepository, $fareCalculator);
$paymentController = new PaymentController();

$authMiddleware = new AuthMiddleware($authService);

$app = AppFactory::create();

$app->post('/signup', [$accountController, 'signup']);
$app->post('/login', [$accountController, 'login']);

$app->get('/account/{id}', [$accountController, 'getAccount'])->add($authMiddleware);
$app->post('/request-ride', [$rideController, 'requestRide'])->add($authMiddleware);
$app->post('/accept-ride', [$rideController, 'acceptRide'])->add($authMiddleware);
$app->post('/start-ride', [$rideController, 'startRide'])->add($authMiddleware);
$app->post('/update-position', [$rideController, 'updatePosition'])->add($authMiddleware);
$app->post('/finish-ride', [$rideController, 'finishRide'])->add($authMiddleware);
$app->get('/ride/{id}', [$rideController, 'getRide'])->add($authMiddleware);
$app->get('/rides', [$rideController, 'getRides'])->add($authMiddleware);
$app->post('/process-payment', [$paymentController, 'processPayment'])->add($authMiddleware);

$app->run();
