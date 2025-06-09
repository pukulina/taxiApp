<?php

use PHPUnit\Framework\TestCase;
use AnnaBozzi\TaxiApp\Utils\CpfValidator;

class CpfValidatorTest extends TestCase
{
	/**
	 * @dataProvider validCpfsProvider
	 */
	public function testValidCpfs(string $cpf): void
	{
		$this->assertTrue(CpfValidator::validate($cpf));
	}

	public static function validCpfsProvider(): array
	{
		return [
			["97456321558"],
			["71428793860"],
			["87748248800"]
		];
	}

	/**
	 * @dataProvider invalidCpfsProvider
	 */
	public function testInvalidCpfs($cpf): void
	{
		$this->assertFalse(CpfValidator::validate($cpf));
	}

	public static function invalidCpfsProvider(): array
	{
		return [
			[""],
			[null],
			["11111111111"],
			["111"],
			["11111111111111"]
		];
	}
}
