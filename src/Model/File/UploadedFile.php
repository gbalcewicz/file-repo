<?php

declare(strict_types=1);

namespace Gb\FileRepo\Model\File;

class UploadedFile
{
    private Path $path;
    private int $size;
    private string $mimeType;
    private Name $originalName;
    private string $checksum;

    public function __construct(
        Path $path,
        string $mimeType,
        Name $originalName
    ) {
        $this->path = $path;
        $this->mimeType = $mimeType;
        $this->originalName = $originalName;

        $absolutePath = $path->asRelativeTo($baseDir);

        if (!file_exists($absolutePath->fullPath())) {
            throw new \InvalidArgumentException("File does not exist");
        }

        $this->size = filesize($absolutePath->fullPath());
        $this->checksum = md5_file($absolutePath->fullPath());
    }

    public function path(): Path
    {
        return $this->path;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function mimeType(): string
    {
        return $this->mimeType;
    }

    public function originalName(): Name
    {
        return $this->originalName;
    }

    public function checksum(): string
    {
        return $this->checksum;
    }
}