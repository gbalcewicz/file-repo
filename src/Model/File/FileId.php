<?php

declare(strict_types=1);

namespace Gb\FileRepo\Model\File;

use Ramsey\Uuid\Uuid;

final class FileId
{
    private string $fileId;

    private function __construct(string $fileId)
    {
        if (!Uuid::isValid($fileId)) {
            throw new \InvalidArgumentException("Given file id is invalid.");
        }

        $this->fileId = $fileId;
    }

    public static function fromString(string $fileId): self
    {
        return new self($fileId);
    }

    public function toString(): string
    {
        return $this->fileId;
    }

    public function equals($other): bool
    {
        if(!$other instanceof self) {
            return false;
        }

        return $this->fileId === $other->fileId;
    }

    public function __toString(): string
    {
        return $this->basketId;
    }
}