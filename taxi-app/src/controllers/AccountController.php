<?php

namespace AnnaBozzi\TaxiApp\Controllers;

use AnnaBozzi\TaxiApp\Repositories\AccountRepository;
use AnnaBozzi\TaxiApp\controllers\SignUp;
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

        $account = $this->accountRepository->getById($accountId);

        if (!$account) {
            $payload = json_encode(['error' => 'Conta não encontrada']);
            $response->getBody()->write($payload);
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode($account));
        return $response->withHeader('Content-Type', 'application/json');

    }

    public function login(Request $request, Response $response) {
        $data = json_decode($request->getBody()->getContents(), true);

        if (!isset($data['email'], $data['password'])) {
            $response->getBody()->write(json_encode(['error' => 'Email and password required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $account = $this->accountRepository->getByEmail($data['email']);

        if (!$account || !$this->accountRepository->verifyPassword($data['email'], $data['password'])) {
            $response->getBody()->write(json_encode(['error' => 'Invalid credentials']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        $authService = new \AnnaBozzi\TaxiApp\Services\AuthService();
        $token = $authService->generateToken(['account_id' => $account->account_id, 'email' => $account->email]);

        $response->getBody()->write(json_encode(['token' => $token, 'account' => $account]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}