<?php

declare(strict_types=1);

namespace Gb\FileRepo\Implementations\Pdo;

use Gb\FileRepo\Model\File;
use Gb\FileRepo\Model\File\FileId;
use Gb\FileRepo\Repository\FileRepositoryInterface;
use Gb\FileRepo\Storage\StorageId;
use PDO;

class FileRepository implements FileRepositoryInterface
{
    private PDO $connection;

    private $columns = [
        'file_id',
        'location',
        'mime_type',
        'base_name',
        'extension',
        'path',
        'created_at',
        'checksum',
        'size',
        'original_name',
        'storage_id'
    ];

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findBySizeAndChecksum(int $size, string $checksum): ?File
    {
        $sql = <<<EOSQL
SELECT %s FROM gb_file WHERE size=:size AND checksum=:checksum LIMIT 1
EOSQL;

        $stmt = $this->connection->prepare(sprintf($sql, implode(',', $this->columns)));
        $stmt->execute([
            ':size' => $size,
            ':checksum' => $checksum
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function save(File $file): void
    {
        $row = $this->toArray($file);
        $params = [];
        foreach ($row as $key => $value) {
            $params[":{$key}"] = $value;
        }

        $values = [];
        foreach ($this->columns as $column) {
            if ($column === 'created_at') {
                $values[] = 'NOW(6)';
            } else {
                $values[] = ":{$column}";
            }
        }

        $sql = sprintf(
            'INSERT INTO gb_file(%s) VALUES(%s)',
            implode(',', $this->columns),
            implode(',', $values)
        );

        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
    }

    public function find(FileId $fileId): ?File
    {
        $sql = <<<EOSQL
SELECT %s FROM gb_file WHERE file_id=:file_id LIMIT 1
EOSQL;

        $stmt = $this->connection->query(sprintf($sql, implode(',', $this->columns)));
        $stmt->execute([
            ':file_id' => $fileId->toString()
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function remove(FileId $fileId): void
    {
        $stmt = $this->connection->prepare('DELETE FROM gb_file WHERE file_id=:file_id');
        $stmt->execute([
            'file_id' => $fileId->toString()
        ]);
    }

    private function hydrate(array $row): File
    {
        return new File(
            FileId::fromString($row['file_id']),
            File\Path::fromFilePath($row['path']),
            File\Name::createFromFullName($row['original_name']),
            $row['mime_type'],
            (int)$row['size'],
            $row['checksum'],
            StorageId::fromString($row['storage_id'])
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
            'storage_id' => $file->storageId()->toString()
        ];
    }
}