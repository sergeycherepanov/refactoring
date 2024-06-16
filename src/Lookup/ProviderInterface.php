<?php

declare(strict_types=1);

namespace App\Lookup;

use App\Lookup\Dto\LookupResult;

interface ProviderInterface
{
    public function lookup(int $bin): LookupResult;
}
