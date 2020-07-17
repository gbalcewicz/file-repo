<?php

declare(strict_types=1);

namespace Gb\FileRepo\Storage;

use Gb\FileRepo\Model\File;
use Gb\FileRepo\Model\File\Path;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface StorageInterface
{
    public function id(): StorageId;
    public function findFile(UploadedFile $uploadedFile): ?File;
    public function storeFile(UploadedFile $uploadedFile): File;
    public function deleteFile(File $file): void;
    public function localPath(File $file): Path;
}