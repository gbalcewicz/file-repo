<?php

declare(strict_types=1);

namespace Gb\FileRepo\Model\File;

class Path
{
    private Directory $dir;

    private Name $name;

    private function __construct(Directory $dir, Name $name)
    {
        $this->dir = $dir;
        $this->name = $name;
    }

    public static function fromFilePath(string $filePath): self
    {
        if (!preg_match('~(.*?)/?([^/]+)$~', $filePath, $m)) {
            throw new \InvalidArgumentException("Given string is not a filepath.");
        }

        return self::createWithFullName(Directory::create($m[1]), $m[2]);
    }

    public static function createWithFullName(Directory $dir, string $fileName): self
    {
        return new self(clone $dir, Name::createFromFullName($fileName));
    }

    public static function create(Directory $dir, Name $name)
    {
        return new self(clone $dir, clone $name);
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function dir(): Directory
    {
        return $this->dir;
    }

    public function fullPath(): string
    {
        if ($this->dir->isEmpty()) {
            return $this->name->toString();
        }
        
        return sprintf('%s/%s', $this->dir->toString(), $this->name->toString());
    }

    public function asRelativeTo(Directory $baseDir)
    {
        return new self($this->dir->asRelativeTo($baseDir), $this->name);
    }
}