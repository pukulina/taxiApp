<?php

namespace AnnaBozzi\TaxiApp\controllers;

use AnnaBozzi\TaxiApp\Repositories\AccountRepository;
use Exception;

class SignUp {
	private AccountRepository $accountRepository;

	public function __construct(AccountRepository $accountRepository) {
		$this->accountRepository = $accountRepository;
	}

	public function handle(array $data): \App\Models\Account {
		if (!isset($data['name'], $data['email'], $data['cpf'], $data['password'])) {
			throw new Exception('Dados inválidos');
		}

		if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
			throw new Exception('Email inválido');
		}

		if ($this->accountRepository->getByEmail($data['email'])) {
			throw new Exception('Email já cadastrado');
		}

		return $this->accountRepository->createAccount(
			$data['name'], $data['email'], $data['cpf'], $data['password'],
			$data['is_passenger'] ?? false, $data['is_driver'] ?? false, $data['car_plate'] ?? null
		);
	}

	public function isValidEmail(string $email): bool {
		return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
	}

	public function isValidName(string $name): bool {
		return preg_match("/^[a-zA-ZÀ-ÿ\s]+$/u", $name);
	}

	public function isValidCarPlate(string $carPlate): bool {
		return preg_match('/^[A-Z]{3}-?[0-9][0-9A-Z][0-9]{2}$/', strtoupper($carPlate));
	}


}
