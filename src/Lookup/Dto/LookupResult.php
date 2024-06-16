<?php

declare(strict_types=1);

namespace App\Lookup\Dto;

final class LookupResult
{
    public function __construct(public readonly string $countryCode) {}
}
