<?php

declare(strict_types=1);

namespace Tests;

use App\Commission;
use App\Lookup\Dto\LookupResult;
use App\Lookup\Lookup;
use App\Lookup\ProviderInterface as LookupProviderInterface;
use Brick\Money\CurrencyConverter;
use Brick\Money\ExchangeRateProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class CommissionTest extends TestCase
{
    public function testCalculateLT(): void
    {
        $lookupProvider = $this->createMock(LookupProviderInterface::class);
        $lookupProvider
            ->expects($this->once())
            ->method('lookup')
            ->with(516793)
            ->willReturn(new LookupResult('LT'))
        ;

        $exchangeProvider = $this->createMock(ExchangeRateProvider::class);
        $exchangeProvider
            ->expects($this->once())
            ->method('getExchangeRate')
            ->with('USD', 'EUR')
            ->willReturn(0.93285061423549)
        ;

        $commission = new Commission(
            new Lookup($lookupProvider),
            new CurrencyConverter($exchangeProvider)
        );

        $value = $commission->calculate(516793, 50.00, 'USD');
        $this->assertEquals(0.47, $value);
    }

    public function testCalculateJP(): void
    {
        $lookupProvider = $this->createMock(LookupProviderInterface::class);
        $lookupProvider
            ->expects($this->once())
            ->method('lookup')
            ->with(45417360)
            ->willReturn(new LookupResult('US'))
        ;

        $exchangeProvider = $this->createMock(ExchangeRateProvider::class);
        $exchangeProvider
            ->expects($this->once())
            ->method('getExchangeRate')
            ->with('JPY', 'EUR')
            ->willReturn(0.005926432982752)
        ;

        $commission = new Commission(
            new Lookup($lookupProvider),
            new CurrencyConverter($exchangeProvider)
        );

        $value = $commission->calculate(45417360, 10000.00, 'JPY');
        $this->assertEquals(1.19, $value);
    }

    public function testCalculateDK(): void
    {
        $lookupProvider = $this->createMock(LookupProviderInterface::class);
        $lookupProvider
            ->expects($this->once())
            ->method('lookup')
            ->with(45717360)
            ->willReturn(new LookupResult('DK'))
        ;

        $exchangeProvider = $this->createMock(ExchangeRateProvider::class);
        $exchangeProvider
            ->expects($this->never())
            ->method('getExchangeRate')
        ;

        $commission = new Commission(
            new Lookup($lookupProvider),
            new CurrencyConverter($exchangeProvider)
        );

        $value = $commission->calculate(45717360, 100.00, 'EUR');
        $this->assertEquals(1, $value);
    }
}
