<?php


namespace Gb\FileRepo\Implementations\Traits;


use Gb\FileRepo\Model\File;
use Gb\FileRepo\Model\File\FileId;
use Gb\FileRepo\Storage\StorageId;

trait Hydrate
{
    private function hydrate(array $row): File
    {
        $arguments = json_decode($row['upload_arguments'] ?? '[]', true);

        return new File(
            FileId::fromString($row['file_id']),
            File\Path::fromFilePath($row['path']),
            File\Name::createFromFullName($row['original_name']),
            $row['mime_type'],
            (int)$row['size'],
            $row['checksum'],
            StorageId::fromString($row['storage_id']),
            File\UniqueKey::fromString($row['unique_key'] ?? $row['file_id']),
            is_array($arguments) ? $arguments : [],
        );
    }

    private function toArray(File $file)
    {
        return [
            'file_id' => $file->fileId()->toString(),
            'location' => $file->path()->dir()->toString(),
            'base_name' => $file->path()->name()->baseName(),
            'extension' => $file->path()->name()->extension(),
            'path' => $file->path()->fullPath(),
            'checksum' => $file->checksum(),
            'size' => $file->size(),
            'mime_type' => $file->mimeType(),
            'original_name' => $file->originalName()->toString(),
            'storage_id' => $file->storageId()->toString(),
            'unique_key' => $file->key()->toString(),
            'upload_arguments' => json_encode($file->uploadArguments())
        ];
    }
}
