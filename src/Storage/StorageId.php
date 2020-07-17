<?php

declare(strict_types=1);

namespace Gb\FileRepo\Storage;

class StorageId
{
    private string $storageId;

    private function __construct(string $storageId)
    {
        $this->storageId = $storageId;
    }

    public static function fromString(string $storageId): self
    {
        return new self($storageId);
    }

    public function toString(): string
    {
        return $this->storageId;
    }

    public function equals($other): bool
    {
        if(!$other instanceof self) {
            return false;
        }

        return $this->storageId === $other->storageId;
    }

    public function __toString(): string
    {
        return $this->storageId;
    }
}