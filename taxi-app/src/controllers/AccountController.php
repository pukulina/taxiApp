<?php

namespace AnnaBozzi\TaxiApp\Controllers;

use AnnaBozzi\TaxiApp\repositories\AccountRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AccountController {
	private SignUp $signUpService;
	private AccountRepository $accountRepository;

	public function __construct(AccountRepository $accountRepository) {
		$this->accountRepository = $accountRepository;
		$this->signUpService = new SignUp($accountRepository);
	}

	public function signup(Request $request, Response $response) {
		$data = json_decode($request->getBody()->getContents(), true);

		try {
			$account = $this->signUpService->handle($data);
			$response->getBody()->write(json_encode($account));
			return $response->withHeader('Content-Type', 'application/json');
		} catch (\Exception $e) {
			$response->getBody()->write(json_encode(['error' => $e->getMessage()]));
			return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
		}
	}
	public function getAccount(Request $request, Response $response, array $args){
		$accountId = $args['id'] ?? null;

		if (!$accountId) {
			$payload = json_encode(['error' => 'ID inválido']);
			$response->getBody()->write($payload);
			return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
		}

		$account = $this->accountRepository->getByEmail($accountId);

		if (!$account) {
			$payload = json_encode(['error' => 'Conta não encontrada']);
			$response->getBody()->write($payload);
			return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
		}

		$response->getBody()->write(json_encode($account));
		return $response->withHeader('Content-Type', 'application/json');

	}
}
