<?php

namespace test\Models;

use App\Models\Account;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    public function testConstructorWithAllFields(): void
    {
        $data = [
            'account_id' => '123e4567-e89b-12d3-a456-426614174000',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'cpf' => '97456321558',
            'car_plate' => 'ABC1234',
            'is_passenger' => true,
            'is_driver' => false
        ];

        $account = new Account($data);

        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000', $account->account_id);
        $this->assertEquals('John Doe', $account->name);
        $this->assertEquals('john@example.com', $account->email);
        $this->assertEquals('97456321558', $account->cpf);
        $this->assertEquals('ABC1234', $account->car_plate);
        $this->assertTrue($account->is_passenger);
        $this->assertFalse($account->is_driver);
    }

    public function testConstructorWithNullCarPlate(): void
    {
        $data = [
            'account_id' => '123e4567-e89b-12d3-a456-426614174000',
            'name' => 'Jane Passenger',
            'email' => 'jane@example.com',
            'cpf' => '12345678900',
            'is_passenger' => true,
            'is_driver' => false
        ];

        $account = new Account($data);

        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000', $account->account_id);
        $this->assertEquals('Jane Passenger', $account->name);
        $this->assertEquals('jane@example.com', $account->email);
        $this->assertEquals('12345678900', $account->cpf);
        $this->assertNull($account->car_plate);
        $this->assertTrue($account->is_passenger);
        $this->assertFalse($account->is_driver);
    }

    public function testConstructorWithDriverAccount(): void
    {
        $data = [
            'account_id' => '123e4567-e89b-12d3-a456-426614174000',
            'name' => 'Bob Driver',
            'email' => 'bob@example.com',
            'cpf' => '11122233344',
            'car_plate' => 'XYZ9876',
            'is_passenger' => false,
            'is_driver' => true
        ];

        $account = new Account($data);

        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000', $account->account_id);
        $this->assertEquals('Bob Driver', $account->name);
        $this->assertEquals('bob@example.com', $account->email);
        $this->assertEquals('11122233344', $account->cpf);
        $this->assertEquals('XYZ9876', $account->car_plate);
        $this->assertFalse($account->is_passenger);
        $this->assertTrue($account->is_driver);
    }

    public function testConstructorWithBothPassengerAndDriver(): void
    {
        $data = [
            'account_id' => '123e4567-e89b-12d3-a456-426614174000',
            'name' => 'Alice Both',
            'email' => 'alice@example.com',
            'cpf' => '55566677788',
            'car_plate' => 'DEF5678',
            'is_passenger' => true,
            'is_driver' => true
        ];

        $account = new Account($data);

        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000', $account->account_id);
        $this->assertEquals('Alice Both', $account->name);
        $this->assertEquals('alice@example.com', $account->email);
        $this->assertEquals('55566677788', $account->cpf);
        $this->assertEquals('DEF5678', $account->car_plate);
        $this->assertTrue($account->is_passenger);
        $this->assertTrue($account->is_driver);
    }

    public function testConstructorWithEmptyCarPlateString(): void
    {
        $data = [
            'account_id' => '123e4567-e89b-12d3-a456-426614174000',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'cpf' => '99988877766',
            'car_plate' => '',
            'is_passenger' => true,
            'is_driver' => false
        ];

        $account = new Account($data);

        $this->assertEquals('', $account->car_plate);
    }
}