<?php

declare(strict_types=1);

namespace Gb\FileRepo\Repository;

use Gb\FileRepo\Model\File;
use Gb\FileRepo\Model\File\FileId;
use Gb\FileRepo\Model\File\UniqueKey;

interface FileRepositoryInterface
{
    public function findByKey(UniqueKey $key): ?File;
    public function save(File $file): void;
    public function find(FileId $fileId): ?File;
    public function remove(FileId $fileId): void;
}
