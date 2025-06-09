<?php

namespace test;

use AnnaBozzi\TaxiApp\Controllers\AccountController;
use AnnaBozzi\TaxiApp\repositories\AccountRepository;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Stream;

class AccountTest extends TestCase {

	public function createPassangerAccount() {
		// Given
		$inputSignup = [
			"name" => "Lucas Almeida da Silva",
			"email" => "lucas.silva89@email.com",
			"cpf" => "473.285.610-39",
			"is_passenger" => true,
			"password" => "123456"
		];

		// When
		$accountRepositoryMock = $this->createPassengerAccountMock(AccountRepository::class);
		$accountController = new AccountController($accountRepositoryMock);

		$requestFactory = new RequestFactory();
		$request = $requestFactory->createRequest('POST', '/signup')
			->withBody(new Stream(fopen('php://temp', 'r+')));
		$request->getBody()->write(json_encode($inputSignup));
		$request->getBody()->rewind();

		$responseFactory = new ResponseFactory();
		$response = $responseFactory->createResponse();

		$response = $accountController->signup($request, $response);

		// Assert
		$this->assertEquals(200, $response->getStatusCode());

		$responseBody = json_decode((string) $response->getBody(), true);
		$this->assertEquals($inputSignup['name'], $responseBody['name']);
		$this->assertEquals($inputSignup['email'], $responseBody['email']);
		$this->assertEquals($inputSignup['cpf'], $responseBody['cpf']);
		$this->assertTrue($responseBody['is_passenger']);
	}

	private function createPassengerAccountMock(string $class) {
		$mock = $this->createMock($class);

		$mock->method('createAccount')->willReturn([
			"name" => "Lucas Almeida da Silva",
			"email" => "lucas.silva89@email.com",
			"cpf" => "473.285.610-39",
			"is_passenger" => true,
			"password" => "123456"
		]);

		return $mock;
	}

	public function testCreatePassengerAccountIntegration() {
		// Configurar um banco de teste real (ex: Postgrees no docker)
		$pdo = new \PDO('sqlite::memory:');
		//$pdo->exec("CREATE TABLE accounts (id INTEGER PRIMARY KEY, name TEXT, email TEXT, cpf TEXT, is_passenger BOOLEAN, password TEXT)");

		//$pdo->exec("CREATE TABLE accounts (id INTEGER PRIMARY KEY, name TEXT, email TEXT, cpf TEXT, is_passenger BOOLEAN, password TEXT)");

		// Criar um repositório real com o banco de teste
		$accountRepository = new AccountRepository($pdo);
		$accountController = new AccountController($accountRepository);

		// Criar requisição real
		$requestFactory = new RequestFactory();
		$request = $requestFactory->createRequest('POST', '/signup')
			->withBody(new Stream(fopen('php://temp', 'r+')));
		$request->getBody()->write(json_encode([
			"name" => "Lucas Almeida da Silva",
			"email" => "lucas.silva89@email.com",
			"cpf" => "473.285.610-39",
			"is_passenger" => true,
			"password" => "123456"
		]));
		$request->getBody()->rewind();

		$responseFactory = new ResponseFactory();
		$response = $responseFactory->createResponse();

		$response = $accountController->signup($request, $response);

		// Verificar resposta HTTP
		$this->assertEquals(200, $response->getStatusCode());

		// Consultar banco para verificar persistência
		$stmt = $pdo->query("SELECT * FROM accounts WHERE email = 'lucas.silva89@email.com'");
		$account = $stmt->fetch(\PDO::FETCH_ASSOC);

		$this->assertNotEmpty($account);
		$this->assertEquals("Lucas Almeida da Silva", $account['name']);
	}

	public function testGetAccount() {}
	public function testGetAccountNotFound() {}

}