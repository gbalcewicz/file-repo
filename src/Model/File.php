<?php

declare(strict_types=1);

namespace Gb\FileRepo\Model;

use Gb\FileRepo\Model\File\FileId;
use Gb\FileRepo\Model\File\Name;
use Gb\FileRepo\Model\File\Path;
use Gb\FileRepo\Storage\StorageId;

class File
{
    private FileId $fileId;
    private Path $path;
    private Name $originalName;
    private string $mimeType;
    private int $size;
    private string $checksum;
    private StorageId $storageId;

    public function __construct(
        FileId $fileId,
        Path $path,
        Name $originalName,
        string $mimeType,
        int $size,
        string $checksum,
        StorageId $storageId
    ) {
        $this->fileId = $fileId;
        $this->path = $path;
        $this->originalName = $originalName;
        $this->mimeType = $mimeType;
        $this->size = $size;
        $this->checksum = $checksum;
        $this->storageId = $storageId;
    }

    public function fileId(): FileId
    {
        return $this->fileId;
    }

    public function path(): Path
    {
        return $this->path;
    }

    public function originalName(): Name
    {
        return $this->originalName;
    }

    public function mimeType(): string
    {
        return $this->mimeType;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function checksum(): string
    {
        return $this->checksum;
    }

    public function storageId(): StorageId
    {
        return $this->storageId;
    }

    public function name(): Name
    {
        return $this->path->name();
    }
}