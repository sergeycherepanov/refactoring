<?php

declare(strict_types=1);

namespace App\Lookup;

use App\Lookup\Dto\LookupResult;

class Lookup
{
    public function __construct(public readonly ProviderInterface $provider)
    {
    }

    public function lookup(int $bin): LookupResult
    {
        return $this->provider->lookup($bin);
    }
}
