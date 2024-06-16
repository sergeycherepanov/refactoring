<?php

declare(strict_types=1);

namespace App\Lookup\Provider\Binlist\Dto;

final class ResultCountry
{
    public function __construct(
        public readonly ?string $numeric,
        public readonly ?string $alpha2,
        public readonly ?string $name,
        public readonly ?string $emoji,
        public readonly ?string $currency,
        public readonly ?string $latitude,
        public readonly ?string $longitude,
    ) {}

    /**
     * @param array<string, mixed> $arr
     * @return self
     */
    public static function fromArray(array $arr): self
    {
        return new self(
            $arr['numeric'] ?? null,
            $arr['alpha2'] ?? null,
            $arr['name'] ?? null,
            $arr['emoji'] ?? null,
            $arr['currency'] ?? null,
            $arr['latitude'] ?? null,
            $arr['longitude'] ?? null,
        );
    }
}
