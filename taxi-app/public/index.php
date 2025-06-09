<?php


require_once __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->get('/', function ($request, $response, $args) {
	$response->getBody()->write("Hello, Taxi App!");
	return $response;
});

$app->run();
