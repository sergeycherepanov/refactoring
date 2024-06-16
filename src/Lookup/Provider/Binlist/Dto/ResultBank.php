<?php

declare(strict_types=1);

namespace App\Lookup\Provider\Binlist\Dto;

final class ResultBank
{
    public function __construct(public readonly ?string $name)
    {
    }

    /**
     * @param array<string, mixed> $arr
     */
    public static function fromArray(array $arr): self
    {
        return new self($arr['name'] ?? null);
    }
}
