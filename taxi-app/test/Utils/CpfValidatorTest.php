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
			["87748248800"],
			["974.563.215-58"],
			["714.287.938-60"],
			["877.482.488-00"]
		];
	}

	/**
	 * @dataProvider invalidCpfsProvider
	 */
	public function testInvalidCpfs($cpf): void
	{
		$this->assertFalse(CpfValidator::validate($cpf));
	}

	public function testValidateWithFormattedCpf(): void
	{
		$this->assertTrue(CpfValidator::validate('974.563.215-58'));
		$this->assertTrue(CpfValidator::validate('714.287.938-60'));
	}

	public function testValidateWithUnformattedCpf(): void
	{
		$this->assertTrue(CpfValidator::validate('97456321558'));
		$this->assertTrue(CpfValidator::validate('71428793860'));
	}

	public function testValidateWithRepeatedDigits(): void
	{
		$this->assertFalse(CpfValidator::validate('11111111111'));
		$this->assertFalse(CpfValidator::validate('22222222222'));
		$this->assertFalse(CpfValidator::validate('00000000000'));
	}

	public function testValidateWithInvalidLength(): void
	{
		$this->assertFalse(CpfValidator::validate('123456789'));
		$this->assertFalse(CpfValidator::validate('123456789012'));
		$this->assertFalse(CpfValidator::validate(''));
	}

	public static function invalidCpfsProvider(): array
	{
		return [
			[""],
			[null],
			["11111111111"],
			["111"],
			["11111111111111"],
			["123.456.789-00"],
			["000.000.000-00"],
			["999.999.999-99"],
			["12345678901"],
			["1234567890a"]
		];
	}
}
