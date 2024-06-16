<?php

declare(strict_types=1);

namespace App\Dto;

final class Line
{
    public function __construct(
        public readonly ?int $bin,
        public readonly ?int $amount,
        public readonly ?string $currency,
    ) {
    }

    /**
     * @param array<string, mixed> $arr
     */
    public static function fromArray(array $arr): self
    {
        return new self(
            isset($arr['bin']) ? (int) $arr['bin'] : null,
            isset($arr['amount']) ? (int) $arr['amount'] : null,
            isset($arr['currency']) ? (string) $arr['currency'] : null
        );
    }
}
