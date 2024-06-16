<?php

declare(strict_types=1);

namespace App\Exchange\Provider\Exchangeratesapi\Dto;

final class Result
{
    /**
     * @param array<string, mixed> $rates
     */
    public function __construct(
        public readonly bool $success,
        public readonly ?int $timestamp,
        public readonly ?string $base,
        public readonly ?string $date,
        public readonly array $rates,
    ) {
    }

    /**
     * @param array<string, mixed> $arr
     */
    public static function fromArray(array $arr): self
    {
        return new self(
            $arr['success'] ?? false,
            $arr['timestamp'] ?? null,
            $arr['base'] ?? null,
            $arr['date'] ?? null,
            $arr['rates'] ?? [],
        );
    }
}
