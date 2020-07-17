<?php

declare(strict_types=1);

namespace Gb\FileRepo\Repository;

use Gb\FileRepo\Model\File;
use Gb\FileRepo\Model\File\FileId;

interface FileRepositoryInterface
{
    public function findBySizeAndChecksum(int $size, string $checksum): ?File;
    public function save(File $file): void;
    public function find(FileId $fileId): ?File;
    public function remove(FileId $fileId): void;
}