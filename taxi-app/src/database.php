<?php

namespace AnnaBozzi\TaxiApp;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class database {
	private PDO $connection;

	public function __construct() {
		$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
		$dotenv->load();

		try {
			$this->connection = new PDO(
				"pgsql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}",
				$_ENV['DB_USER'],
				$_ENV['DB_PASS'],
				[
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //erro => exception
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
				]
			);
		} catch (PDOException $e) {
			die("Erro na conexão: " . $e->getMessage());
		}
	}

	public function getConnection(): PDO {
		return $this->connection;
	}
}
