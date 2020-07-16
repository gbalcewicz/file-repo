<?php

declare(strict_types=1);

namespace Gb\FileRepo\Model\File;

class Directory
{
    private ?string $path;

    private function __construct(string $path)
    {
        $this->path = rtrim($path, '/ ');
        if (strlen($this->path) === 0 || $this->path === '.') {
            $this->path = null;
        }
    }

    public static function create(string $path): self
    {
        return new self($path);
    }

    public function asRelativeTo(Directory $base)
    {
        if ($base->isEmpty()) {
            return clone $this;
        } else if ($this->isEmpty()) {
            return clone $base;
        }

        return new Directory(sprintf('%s/%s', $base->path, $this->path));
    }

    public function toString(): string
    {
        return $this->path ?? '.';
    }

    public function isEmpty(): bool
    {
        return !$this->path;
    }
}