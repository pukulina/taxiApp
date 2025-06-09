<?php

use AnnaBozzi\TaxiApp\Repositories\AccountRepository;
use Slim\Factory\AppFactory;
use AnnaBozzi\TaxiApp\controllers\AccountController;

require __DIR__ . '/../vendor/autoload.php';

$pdo = require __DIR__ . '/../config/database.php';
$accountRepository = new AccountRepository($pdo);
$accountController = new AccountController($accountRepository);

$app = AppFactory::create();

$app->post('/signup', [$accountController, 'signup']);
$app->get('/account/{id}', [$accountController, 'getAccount']);

$app->run();
