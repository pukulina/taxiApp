<?php

namespace test\Repositories;

use AnnaBozzi\TaxiApp\Repositories\AccountRepository;
use App\Models\Account;
use PHPUnit\Framework\TestCase;
use PDO;

class AccountRepositoryTest extends TestCase
{
	private PDO $pdo;
	private AccountRepository $accountRepository;

	protected function setUp(): void
	{
		$this->pdo = new PDO('sqlite::memory:');

		$this->pdo->exec("
			CREATE TABLE account (
				account_id TEXT PRIMARY KEY,
				name TEXT NOT NULL,
				email TEXT NOT NULL UNIQUE,
				cpf TEXT NOT NULL,
				car_plate TEXT,
				is_passenger BOOLEAN NOT NULL DEFAULT 0,
				is_driver BOOLEAN NOT NULL DEFAULT 0,
				password TEXT NOT NULL
			)
		");
		
		$this->accountRepository = new AccountRepository($this->pdo);
	}

	public function testCreateAccountPassenger(): void
	{
		$account = $this->accountRepository->createAccount(
			'John Doe',
			'john@example.com',
			'97456321558',
			'123456',
			true,
			false
		);

		$this->assertInstanceOf(Account::class, $account);
		$this->assertEquals('John Doe', $account->name);
		$this->assertEquals('john@example.com', $account->email);
		$this->assertEquals('97456321558', $account->cpf);
		$this->assertTrue($account->is_passenger);
		$this->assertFalse($account->is_driver);
		$this->assertNull($account->car_plate);
		$this->assertNotEmpty($account->account_id);
	}

	public function testCreateAccountDriver(): void
	{
		$account = $this->accountRepository->createAccount(
			'Jane Driver',
			'jane@example.com',
			'12345678900',
			'123456',
			false,
			true,
			'ABC1234'
		);

		$this->assertInstanceOf(Account::class, $account);
		$this->assertEquals('Jane Driver', $account->name);
		$this->assertEquals('jane@example.com', $account->email);
		$this->assertEquals('12345678900', $account->cpf);
		$this->assertFalse($account->is_passenger);
		$this->assertTrue($account->is_driver);
		$this->assertEquals('ABC1234', $account->car_plate);
	}

	public function testGetByEmailExists(): void
	{
		$this->accountRepository->createAccount(
			'John Doe',
			'john@example.com',
			'97456321558',
			'123456',
			true,
			false
		);

		$account = $this->accountRepository->getByEmail('john@example.com');

		$this->assertInstanceOf(Account::class, $account);
		$this->assertEquals('John Doe', $account->name);
		$this->assertEquals('john@example.com', $account->email);
	}

	public function testGetByEmailNotExists(): void
	{
		$account = $this->accountRepository->getByEmail('nonexistent@example.com');
		$this->assertNull($account);
	}

	public function testGetByIdExists(): void
	{
		$createdAccount = $this->accountRepository->createAccount(
			'John Doe',
			'john@example.com',
			'97456321558',
			'123456',
			true,
			false
		);

		$account = $this->accountRepository->getById($createdAccount->account_id);

		$this->assertInstanceOf(Account::class, $account);
		$this->assertEquals($createdAccount->account_id, $account->account_id);
		$this->assertEquals('John Doe', $account->name);
		$this->assertEquals('john@example.com', $account->email);
	}

	public function testGetByIdNotExists(): void
	{
		$account = $this->accountRepository->getById('non-existent-uuid');
		$this->assertNull($account);
	}

	public function testPasswordIsHashed(): void
	{
		$plainPassword = '123456';
		$this->accountRepository->createAccount(
			'John Doe',
			'john@example.com',
			'97456321558',
			$plainPassword,
			true,
			false
		);

		$stmt = $this->pdo->prepare("SELECT password FROM account WHERE email = :email");
		$stmt->execute(['email' => 'john@example.com']);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		$this->assertNotEquals($plainPassword, $result['password']);
		$this->assertTrue(password_verify($plainPassword, $result['password']));
	}

	public function testUuidGeneration(): void
	{
		$account1 = $this->accountRepository->createAccount(
			'John Doe',
			'john@example.com',
			'97456321558',
			'123456',
			true,
			false
		);

		$account2 = $this->accountRepository->createAccount(
			'Jane Doe',
			'jane@example.com',
			'12345678900',
			'123456',
			true,
			false
		);

		$this->assertNotEquals($account1->account_id, $account2->account_id);
		$this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $account1->account_id);
		$this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $account2->account_id);
	}
}
