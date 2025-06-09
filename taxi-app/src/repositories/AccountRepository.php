<?php

namespace AnnaBozzi\TaxiApp\Repositories;

use App\Models\Account;
use PDO;
use Ramsey\Uuid\Uuid;

class AccountRepository {
	private PDO $pdo;

	public function __construct(PDO $pdo) {
		$this->pdo = $pdo;
	}

	public function createAccount($name, $email, $cpf, $password, $is_passenger, $is_driver, $car_plate = null) {
		$uuid = Uuid::uuid4()->toString();
		$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

		$stmt = $this->pdo->prepare("
            INSERT INTO cccat14.account (account_id, name, email, cpf, car_plate, is_passenger, is_driver, password)
            VALUES (:account_id, :name, :email, :cpf, :car_plate, :is_passenger, :is_driver, :password)
        ");

		$stmt->execute([
			'account_id' => $uuid,
			'name' => $name,
			'email' => $email,
			'cpf' => $cpf,
			'car_plate' => $car_plate,
			'is_passenger' => $is_passenger,
			'is_driver' => $is_driver,
			'password' => $hashedPassword
		]);

		return new Account([
			'account_id' => $uuid,
			'name' => $name,
			'email' => $email,
			'cpf' => $cpf,
			'car_plate' => $car_plate,
			'is_passenger' => $is_passenger,
			'is_driver' => $is_driver
		]);
	}

	public function getByEmail($email) {
		$stmt = $this->pdo->prepare("SELECT * FROM cccat14.account WHERE email = :email");
		$stmt->execute(['email' => $email]);
		$data = $stmt->fetch(PDO::FETCH_ASSOC);

		return $data ? new Account($data) : null;
	}

	public function getById(string $accountId) {
		$stmt = $this->pdo->prepare("SELECT * FROM cccat14.account WHERE account_id = :account_id");
		$stmt->execute(['account_id' => $accountId]);
		$data = $stmt->fetch(PDO::FETCH_ASSOC);

		return $data ? new Account($data) : null;
	}
}
