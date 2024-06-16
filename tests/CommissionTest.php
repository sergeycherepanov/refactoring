<?php
declare(strict_types=1);

use App\Commission;
use App\Lookup\Dto\LookupResult;
use App\Lookup\Lookup;
use App\Lookup\ProviderInterface as LookupProviderInterface;
use Brick\Money\ExchangeRateProvider;
use Brick\Money\CurrencyConverter;
use PHPUnit\Framework\TestCase;

final class CommissionTest extends TestCase
{
    /**
     * @throws \Brick\Money\Exception\CurrencyConversionException
     * @throws \Brick\Math\Exception\RoundingNecessaryException
     * @throws \Brick\Math\Exception\MathException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     * @throws \Brick\Math\Exception\NumberFormatException
     */
    public function testCalculateLT(): void
    {
        $lookupProvider = $this->createMock(LookupProviderInterface::class);
        $lookupProvider
            ->expects($this->once())
            ->method('lookup')
            ->with(516793)
            ->willReturn(new LookupResult('LT'));

        $exchangeProvider = $this->createMock(ExchangeRateProvider::class);
        $exchangeProvider
            ->expects($this->once())
            ->method('getExchangeRate')
            ->with('USD', 'EUR')
            ->willReturn(0.93285061423549);

        $commission = new Commission(
            new Lookup($lookupProvider),
            new CurrencyConverter($exchangeProvider)
        );

        $value = $commission->calculate(516793, 50.00, 'USD');
        $this->assertEquals(0.47, $value);
    }

    /**
     * @throws \Brick\Money\Exception\CurrencyConversionException
     * @throws \Brick\Math\Exception\RoundingNecessaryException
     * @throws \Brick\Math\Exception\MathException
     * @throws \Brick\Money\Exception\UnknownCurrencyException
     * @throws \Brick\Math\Exception\NumberFormatException
     */
    public function testCalculateJP(): void
    {
        $lookupProvider = $this->createMock(LookupProviderInterface::class);
        $lookupProvider
            ->expects($this->once())
            ->method('lookup')
            ->with(45417360)
            ->willReturn(new LookupResult('US'));

        $exchangeProvider = $this->createMock(ExchangeRateProvider::class);
        $exchangeProvider
            ->expects($this->once())
            ->method('getExchangeRate')
            ->with('JPY', 'EUR')
            ->willReturn(0.005926432982752);

        $commission = new Commission(
            new Lookup($lookupProvider),
            new CurrencyConverter($exchangeProvider)
        );

        $value = $commission->calculate(45417360, 10000.00, 'JPY');
        $this->assertEquals(1.19, $value);
    }
}
