<?php

namespace AnnaBozzi\TaxiApp\Repositories;

use PDO;
use Ramsey\Uuid\Uuid;
use AnnaBozzi\TaxiApp\Models\Account;

class AccountRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function createAccount(string $name, string $email, string $cpf, string $password, ?string $carPlate = null, bool $isPassenger = false, bool $isDriver = false): Account {
        $accountId = Uuid::uuid4()->toString();
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->pdo->prepare("
            INSERT INTO account (account_id, name, email, cpf, car_plate, is_passenger, is_driver, password)
            VALUES (:account_id, :name, :email, :cpf, :car_plate, :is_passenger, :is_driver, :password)
        ");

        $stmt->execute([
            'account_id' => $accountId,
            'name' => $name,
            'email' => $email,
            'cpf' => $cpf,
            'car_plate' => $carPlate,
            'is_passenger' => $isPassenger ? 'true' : 'false',
            'is_driver' => $isDriver ? 'true' : 'false',
            'password' => $hashedPassword
        ]);

        return new Account([
            'account_id' => $accountId,
            'name' => $name,
            'email' => $email,
            'cpf' => $cpf,
            'car_plate' => $carPlate,
            'is_passenger' => $isPassenger,
            'is_driver' => $isDriver
        ]);
    }

    public function getByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM account WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new Account($data) : null;
    }

    public function getById(string $accountId) {
        $stmt = $this->pdo->prepare("SELECT * FROM account WHERE account_id = :account_id");
        $stmt->execute(['account_id' => $accountId]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? new Account($data) : null;
    }

    public function verifyPassword(string $email, string $password): bool {
        $stmt = $this->pdo->prepare("SELECT password FROM account WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) return false;
        
        return password_verify($password, $result['password']);
    }
}
