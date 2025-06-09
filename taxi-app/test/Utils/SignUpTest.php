<?php

use AnnaBozzi\TaxiApp\Application\GetAccount;
use AnnaBozzi\TaxiApp\Controllers\SignUp;
use AnnaBozzi\TaxiApp\Repositories\AccountRepository;
use PHPUnit\Framework\TestCase;
use AnnaBozzi\TaxiApp\database;

class SignUpTest extends TestCase
{
	private SignUp $signup;
	private GetAccount $getAccount;

	protected function setUp(): void
	{
		$database = new database();
		$pdo = $database->getConnection();
		$accountRepository = new AccountRepository($pdo);
		$accountRepository = new AccountRepository($pdo);
		$this->signup = new SignUp($accountRepository);
		$this->getAccount = new GetAccount($accountRepository);
	}

	public function testShouldCreatePassengerAccount(): void
	{
		$email = "john.doe" . rand(1, 1000000) . "@gmail.com";

		$input = [
			'name' => 'John Doe',
			'email' => $email,
			'cpf' => '97456321558',
			'is_passenger' => true,
			'password' => '123456'
		];

		$account = $this->signup->handle($input);
		$fetched = $this->getAccount->execute($account->account_id);

		$this->assertEquals($input['name'], $fetched->name);
		$this->assertEquals($input['email'], $fetched->email);
	}

	public function testShouldNotCreateWithInvalidEmail(): void
	{
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Email inválido');

		$input = [
			'name' => 'John Doe',
			'email' => 'john.doe',
			'cpf' => '97456321558',
			'is_passenger' => true,
			'password' => '123456'
		];

		$this->signup->handle($input);
	}

	public function testShouldNotCreateWithDuplicatedEmail(): void
	{
		$email = "john.doe" . rand(1, 1000000) . "@gmail.com";

		$input = [
			'name' => 'John Doe',
			'email' => $email,
			'cpf' => '97456321558',
			'is_passenger' => true,
			'password' => '123456'
		];

		$this->signup->handle($input);

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Email já cadastrado');

		$this->signup->handle($input);
	}

	public function testShouldCreateDriverAccount(): void
	{
		$email = "driver" . rand(1, 1000000) . "@gmail.com";

		$input = [
			'name' => 'John Driver',
			'email' => $email,
			'cpf' => '97456321558',
			'is_passenger' => false,
			'is_driver' => true,
			'car_plate' => 'AAA9999',
			'password' => '123456'
		];

		$account = $this->signup->handle($input);
		$fetched = $this->getAccount->execute($account->account_id);

		$this->assertEquals($input['name'], $fetched->name);
		$this->assertEquals($input['email'], $fetched->email);
	}

	public function testShouldNotCreateDriverWithInvalidCarPlate(): void
	{
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Invalid car plate');

		$input = [
			'name' => 'John Driver',
			'email' => 'driver' . rand(1, 1000000) . '@gmail.com',
			'cpf' => '97456321558',
			'is_passenger' => false,
			'is_driver' => true,
			'car_plate' => 'ABC123',
			'password' => '123456'
		];

		$this->signup->handle($input);
	}
}
