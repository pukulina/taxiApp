<?php

namespace AnnaBozzi\TaxiApp\Test\Api;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class ApiTest extends TestCase
{
	private Client $http;

	protected function setUp(): void
	{
		$this->http = new Client([
			'base_uri' => 'http://taxi-app-nginx',
			'http_errors' => false
		]);
	}

	/**
	 * @dataProvider validPassengers
	 */
	public function testShouldCreatePassengerAccount($input)
	{
		$response = $this->http->post('/signup', ['json' => $input]);
		$this->assertEquals(201, $response->getStatusCode());

		$data = json_decode($response->getBody(), true);
		$this->assertArrayHasKey('accountId', $data);

		$getResponse = $this->http->get("/accounts/{$data['accountId']}");
		$getData = json_decode($getResponse->getBody(), true);

		$this->assertEquals($input['name'], $getData['name']);
		$this->assertEquals($input['email'], $getData['email']);
	}

	public function testShouldNotCreatePassengerWithInvalidName()
	{
		$input = [
			'name' => 'John',
			'email' => 'john'.uniqid().'@gmail.com',
			'cpf' => '97456321558',
			'isPassenger' => true,
			'password' => '123456'
		];

		$response = $this->http->post('/signup', ['json' => $input]);
		$this->assertEquals(422, $response->getStatusCode());

		$data = json_decode($response->getBody(), true);
		$this->assertEquals('Invalid name', $data['message']);
	}

	public function testShouldCreateDriverAccount()
	{
		$input = [
			'name' => 'Jane Doe',
			'email' => 'jane'.uniqid().'@gmail.com',
			'cpf' => '21345678909',
			'carPlate' => 'AAA1234',
			'isPassenger' => false,
			'isDriver' => true,
			'password' => '123456'
		];

		$response = $this->http->post('/signup', ['json' => $input]);
		$this->assertEquals(201, $response->getStatusCode());

		$data = json_decode($response->getBody(), true);
		$this->assertArrayHasKey('accountId', $data);

		$getResponse = $this->http->get("/accounts/{$data['accountId']}");
		$getData = json_decode($getResponse->getBody(), true);

		$this->assertEquals($input['name'], $getData['name']);
		$this->assertEquals($input['email'], $getData['email']);
	}

	public function testShouldNotCreateDriverWithInvalidPlate()
	{
		$input = [
			'name' => 'Jane Driver',
			'email' => 'jane'.uniqid().'@gmail.com',
			'cpf' => '21345678909',
			'carPlate' => 'AAA123',
			'isPassenger' => false,
			'isDriver' => true,
			'password' => '123456'
		];

		$response = $this->http->post('/signup', ['json' => $input]);
		$this->assertEquals(422, $response->getStatusCode());

		$data = json_decode($response->getBody(), true);
		$this->assertEquals('Invalid car plate', $data['message']);
	}

	public static function validPassengers(): array
	{
		return [[
			[
				'name' => 'John Doe',
				'email' => 'john'.uniqid().'@gmail.com',
				'cpf' => '97456321558',
				'isPassenger' => true,
				'password' => '123456'
			]
		]];
	}
}
