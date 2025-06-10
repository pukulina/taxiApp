<?php

namespace test\Controllers;

use AnnaBozzi\TaxiApp\Controllers\AccountController;
use AnnaBozzi\TaxiApp\Repositories\AccountRepository;
use App\Models\Account;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Stream;

class AccountControllerTest extends TestCase
{
	private AccountController $accountController;
	private AccountRepository $accountRepositoryMock;

	protected function setUp(): void
	{
		$this->accountRepositoryMock = $this->createMock(AccountRepository::class);
		$this->accountController = new AccountController($this->accountRepositoryMock);
	}

	public function testSignupSuccess(): void
	{
		$inputData = [
			'name' => 'John Doe',
			'email' => 'john@example.com',
			'cpf' => '97456321558',
			'is_passenger' => true,
			'password' => '123456'
		];

		$expectedAccount = new Account([
			'account_id' => '123e4567-e89b-12d3-a456-426614174000',
			'name' => 'John Doe',
			'email' => 'john@example.com',
			'cpf' => '97456321558',
			'car_plate' => null,
			'is_passenger' => true,
			'is_driver' => false
		]);

		$this->accountRepositoryMock
			->method('getByEmail')
			->with('john@example.com')
			->willReturn(null);

		$this->accountRepositoryMock
			->method('createAccount')
			->willReturn($expectedAccount);

		$requestFactory = new RequestFactory();
		$request = $requestFactory->createRequest('POST', '/signup')
			->withBody(new Stream(fopen('php://temp', 'r+')));
		$request->getBody()->write(json_encode($inputData));
		$request->getBody()->rewind();

		$responseFactory = new ResponseFactory();
		$response = $responseFactory->createResponse();

		$result = $this->accountController->signup($request, $response);

		$this->assertEquals(200, $result->getStatusCode());
		$this->assertEquals('application/json', $result->getHeaderLine('Content-Type'));

		$responseData = json_decode((string) $result->getBody(), true);
		$this->assertEquals('John Doe', $responseData['name']);
		$this->assertEquals('john@example.com', $responseData['email']);
	}

	public function testSignupWithInvalidEmail(): void
	{
		$inputData = [
			'name' => 'John Doe',
			'email' => 'invalid-email',
			'cpf' => '97456321558',
			'is_passenger' => true,
			'password' => '123456'
		];

		$requestFactory = new RequestFactory();
		$request = $requestFactory->createRequest('POST', '/signup')
			->withBody(new Stream(fopen('php://temp', 'r+')));
		$request->getBody()->write(json_encode($inputData));
		$request->getBody()->rewind();

		$responseFactory = new ResponseFactory();
		$response = $responseFactory->createResponse();

		$result = $this->accountController->signup($request, $response);

		$this->assertEquals(400, $result->getStatusCode());
		$this->assertEquals('application/json', $result->getHeaderLine('Content-Type'));

		$responseData = json_decode((string) $result->getBody(), true);
		$this->assertEquals('Email inválido', $responseData['error']);
	}

	public function testSignupWithDuplicateEmail(): void
	{
		$inputData = [
			'name' => 'John Doe',
			'email' => 'john@example.com',
			'cpf' => '97456321558',
			'is_passenger' => true,
			'password' => '123456'
		];

		$existingAccount = new Account([
			'account_id' => '123e4567-e89b-12d3-a456-426614174000',
			'name' => 'Existing User',
			'email' => 'john@example.com',
			'cpf' => '12345678900',
			'car_plate' => null,
			'is_passenger' => true,
			'is_driver' => false
		]);

		$this->accountRepositoryMock
			->method('getByEmail')
			->with('john@example.com')
			->willReturn($existingAccount);

		$requestFactory = new RequestFactory();
		$request = $requestFactory->createRequest('POST', '/signup')
			->withBody(new Stream(fopen('php://temp', 'r+')));
		$request->getBody()->write(json_encode($inputData));
		$request->getBody()->rewind();

		$responseFactory = new ResponseFactory();
		$response = $responseFactory->createResponse();

		$result = $this->accountController->signup($request, $response);

		$this->assertEquals(400, $result->getStatusCode());
		$responseData = json_decode((string) $result->getBody(), true);
		$this->assertEquals('Email já cadastrado', $responseData['error']);
	}

	public function testGetAccountSuccess(): void
	{
		$accountId = '123e4567-e89b-12d3-a456-426614174000';
		$expectedAccount = new Account([
			'account_id' => $accountId,
			'name' => 'John Doe',
			'email' => 'john@example.com',
			'cpf' => '97456321558',
			'car_plate' => null,
			'is_passenger' => true,
			'is_driver' => false
		]);

		$this->accountRepositoryMock
			->method('getByEmail')
			->with($accountId)
			->willReturn($expectedAccount);

		$requestFactory = new RequestFactory();
		$request = $requestFactory->createRequest('GET', "/account/{$accountId}");

		$responseFactory = new ResponseFactory();
		$response = $responseFactory->createResponse();

		$result = $this->accountController->getAccount($request, $response, ['id' => $accountId]);

		$this->assertEquals(200, $result->getStatusCode());
		$this->assertEquals('application/json', $result->getHeaderLine('Content-Type'));

		$responseData = json_decode((string) $result->getBody(), true);
		$this->assertEquals('John Doe', $responseData['name']);
		$this->assertEquals('john@example.com', $responseData['email']);
	}

	public function testGetAccountNotFound(): void
	{
		$accountId = 'non-existent-id';

		$this->accountRepositoryMock
			->method('getByEmail')
			->with($accountId)
			->willReturn(null);

		$requestFactory = new RequestFactory();
		$request = $requestFactory->createRequest('GET', "/account/{$accountId}");

		$responseFactory = new ResponseFactory();
		$response = $responseFactory->createResponse();

		$result = $this->accountController->getAccount($request, $response, ['id' => $accountId]);

		$this->assertEquals(404, $result->getStatusCode());
		$responseData = json_decode((string) $result->getBody(), true);
		$this->assertEquals('Conta não encontrada', $responseData['error']);
	}

	public function testGetAccountWithInvalidId(): void
	{
		$requestFactory = new RequestFactory();
		$request = $requestFactory->createRequest('GET', '/account/');

		$responseFactory = new ResponseFactory();
		$response = $responseFactory->createResponse();

		$result = $this->accountController->getAccount($request, $response, []);

		$this->assertEquals(400, $result->getStatusCode());
		$responseData = json_decode((string) $result->getBody(), true);
		$this->assertEquals('ID inválido', $responseData['error']);
	}
}
