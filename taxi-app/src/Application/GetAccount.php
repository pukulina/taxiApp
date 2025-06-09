<?php

namespace AnnaBozzi\TaxiApp\Application;

use AnnaBozzi\TaxiApp\Repositories\AccountRepository;

class GetAccount
{
	private AccountRepository $accountRepository;

	public function __construct(AccountRepository $accountRepository)
	{
		$this->accountRepository = $accountRepository;
	}

	public function execute(string $accountId): \App\Models\Account {
		return $this->accountRepository->getById($accountId);
	}
}
