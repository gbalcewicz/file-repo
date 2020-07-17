<?php

declare(strict_types=1);

namespace Gb\FileRepo\Model\Repository;

use Ramsey\Uuid\Uuid;

class RepositoryId
{
    private string $repositoryId;

    private function __construct(string $fileId)
    {
        if (!Uuid::isValid($fileId)) {
            throw new \InvalidArgumentException("Given repository id is invalid.");
        }

        $this->repositoryId = $fileId;
    }

    public static function fromString(string $fileId): self
    {
        return new self($fileId);
    }

    public function toString(): string
    {
        return $this->repositoryId;
    }

    public function equals($other): bool
    {
        if(!$other instanceof self) {
            return false;
        }

        return $this->repositoryId === $other->repositoryId;
    }

    public function __toString(): string
    {
        return $this->repositoryId;
    }
}