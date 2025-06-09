<?php

namespace test\Application;

use AnnaBozzi\TaxiApp\Application\GetAccount;
use AnnaBozzi\TaxiApp\Repositories\AccountRepository;
use App\Models\Account;
use PHPUnit\Framework\TestCase;

class GetAccountTest extends TestCase
{
    private GetAccount $getAccount;
    private AccountRepository $accountRepositoryMock;

    protected function setUp(): void
    {
        $this->accountRepositoryMock = $this->createMock(AccountRepository::class);
        $this->getAccount = new GetAccount($this->accountRepositoryMock);
    }

    public function testExecuteSuccess(): void
    {
        $accountId = '123e4567-e89b-12d3-a456-426614174000';
        $expectedAccount = new Account([
            'account_id' => $accountId,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'cpf' => '97456321558',
            'car_plate' => null,
            'is_passenger' => true,
            'is_driver' => false
        ]);

        $this->accountRepositoryMock
            ->method('getById')
            ->with($accountId)
            ->willReturn($expectedAccount);

        $result = $this->getAccount->execute($accountId);

        $this->assertInstanceOf(Account::class, $result);
        $this->assertEquals($accountId, $result->account_id);
        $this->assertEquals('John Doe', $result->name);
        $this->assertEquals('john@example.com', $result->email);
    }

    public function testExecuteWithNonExistentAccount(): void
    {
        $accountId = 'non-existent-id';

        $this->accountRepositoryMock
            ->method('getById')
            ->with($accountId)
            ->willReturn(null);

        $result = $this->getAccount->execute($accountId);

        $this->assertNull($result);
    }

    public function testExecuteCallsRepositoryWithCorrectId(): void
    {
        $accountId = '123e4567-e89b-12d3-a456-426614174000';

        $this->accountRepositoryMock
            ->expects($this->once())
            ->method('getById')
            ->with($this->equalTo($accountId))
            ->willReturn(null);

        $this->getAccount->execute($accountId);
    }
}