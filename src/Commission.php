<?php

declare(strict_types=1);

namespace App;

use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Money\Context\CashContext;
use Brick\Money\CurrencyConverter;
use Brick\Money\Exception\CurrencyConversionException;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;
use Brick\Math\RoundingMode;

use App\Exchange\Exchange;
use App\Lookup\Lookup;

class Commission
{
    public function __construct(
        private readonly Lookup $lookup,
        private readonly CurrencyConverter $exchange
    ) {
    }

    /**
     * @param Money $money
     * @param string $toCurrency
     * @return Money
     * @throws CurrencyConversionException
     * @throws RoundingNecessaryException
     */
    protected function convertAmount(Money $money, string $toCurrency): Money
    {
        return $this->exchange->convert($money, $toCurrency, roundingMode: RoundingMode::UP);
    }

    private function isEu(int $bin): bool
    {
        $countryCode = $this->lookup->lookup($bin)->countryCode;

        return match ($countryCode) {
            'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PO', 'PT', 'RO', 'SE', 'SI', 'SK' => true,
            default => false,
        };
    }

    /**
     * @throws CurrencyConversionException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     * @throws NumberFormatException
     * @throws MathException
     */
    public function calculate(int $bin, float $amount, string $currency): float
    {
        $money = Money::of($amount, $currency, new CashContext(step: 1));

        if ('EUR' !== $currency) {
            $money = $this->convertAmount($money, 'EUR');
        }

        $fee = $this->isEu($bin) ? 0.01 : 0.02;

        return $money->multipliedBy($fee, RoundingMode::UP)->getAmount()->toFloat();
    }
}
