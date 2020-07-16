<?php

declare(strict_types=1);

namespace Gb\FileRepo\Model\File;

class Name
{
    private string $baseName;

    private ?string $extension;

    private function __construct(string $baseName, string $extension = null)
    {
        $this->baseName = $baseName;
        $this->extension = $extension;
    }

    public static function createFromFullName(string $name): self
    {
        if (strpos($name, '/') !== false) {
            throw new \InvalidArgumentException("Invalid name");
        }

        if (preg_match('/^(.+)\.(.+)$/', $name, $nameMatch)) {
            return new self($nameMatch[1], $nameMatch[2]);
        }
        
        return new self($name);
    }

    public static function create(string $baseName, string $extension = null): self
    {
        return new self($baseName, $extension);
    }    

    public function toString(): string
    {
        if ($this->extension) {
            return sprintf('%s.%s', $this->baseName, $this->extension);
        }

        return $this->baseName;
    }

    public function baseName(): string
    {
        return $this->baseName;
    }

    public function extension(): ?string
    {
        return $this->extension;
    }
}