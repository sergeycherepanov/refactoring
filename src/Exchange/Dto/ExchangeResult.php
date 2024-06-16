<?php

declare(strict_types=1);

namespace App\Exchange\Dto;

final class ExchangeResult
{
    /**
     * @param array<string, float> $rates
     */
    public function __construct(public readonly array $rates) {}
}
