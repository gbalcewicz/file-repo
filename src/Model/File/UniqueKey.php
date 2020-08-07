<?php

declare(strict_types=1);

namespace Gb\FileRepo\Model\File;

final class UniqueKey
{
    private string $uniqueKey;

    private function __construct(string $uniqueKey)
    {
        $this->uniqueKey = $uniqueKey;
    }

    public static function fromString(string $uniqueKey): self
    {
        return new self($uniqueKey);
    }

    public function toString(): string
    {
        return $this->uniqueKey;
    }

    public function equals($other): bool
    {
        if(!$other instanceof self) {
            return false;
        }

        return $this->uniqueKey === $other->uniqueKey;
    }

    public function __toString(): string
    {
        return $this->uniqueKey;
    }
}
