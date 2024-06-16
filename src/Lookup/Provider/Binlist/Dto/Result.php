<?php

declare(strict_types=1);

namespace App\Lookup\Provider\Binlist\Dto;

final class Result
{
    public function __construct(
        public readonly ?string $number,
        public readonly ?string $scheme,
        public readonly ?string $type,
        public readonly ?string $brand,
        public readonly ResultCountry $country,
        public readonly ResultBank $bank,
    ) {
    }

    /**
     * @param array<string, mixed> $arr
     */
    public static function fromArray(array $arr): self
    {
        return new self(
            $arr['number'] ?? null,
            $arr['scheme'] ?? null,
            $arr['type'] ?? null,
            $arr['brand'] ?? null,
            ResultCountry::fromArray($arr['country'] ?? []),
            ResultBank::fromArray($arr['bank'] ?? []),
        );
    }
}
