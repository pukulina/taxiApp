<?php

namespace AnnaBozzi\TaxiApp\Utils;

class CpfValidator
{
	public static function validate(?string  $cpf): bool
	{
		if (!$cpf) return false;

		$cpf = self::clean($cpf);

		if (self::isInvalidLength($cpf)) return false;
		if (self::allDigitsAreTheSame($cpf)) return false;

		$dg1 = self::calculateDigit($cpf, 10);
		$dg2 = self::calculateDigit($cpf, 11);

		return self::extractCheckDigit($cpf) === "{$dg1}{$dg2}";
	}

	private static function clean(string $cpf): string
	{
		return preg_replace('/\D/', '', $cpf);
	}

	private static function isInvalidLength(string $cpf): bool
	{
		return strlen($cpf) !== 11;
	}

	private static function allDigitsAreTheSame(string $cpf): bool
	{
		return preg_match('/^(\d)\1{10}$/', $cpf) === 1;
	}

	private static function calculateDigit(string $cpf, int $factor): int
	{
		$total = 0;

		for ($i = 0; $i < strlen($cpf) && $factor > 1; $i++) {
			$total += intval($cpf[$i]) * $factor--;
		}

		$rest = $total % 11;
		return ($rest < 2) ? 0 : 11 - $rest;
	}

	private static function extractCheckDigit(string $cpf): string
	{
		return substr($cpf, 9, 2);
	}
}
