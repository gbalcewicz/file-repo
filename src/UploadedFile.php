<?php

namespace Gb\FileRepo;

use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

class UploadedFile
{
    private SymfonyUploadedFile $uploadedFile;
    private array $arguments;

    public function __construct(SymfonyUploadedFile $uploadedFile, array $arguments = [])
    {
        $this->uploadedFile = $uploadedFile;
        $this->arguments = $arguments;
    }

    public function getWrappedUploadedFile(): SymfonyUploadedFile
    {
        return $this->uploadedFile;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function hasArgument(string $name)
    {
        return isset($this->arguments[$name]);
    }

    public function getArgument(string $name, $default = null)
    {
        return $this->arguments[$name] ?? $default;
    }

    public function setArgument(string $name, $value)
    {
        return $this->arguments[$name] = $value;
    }
}
