<?php

declare(strict_types=1);

namespace Gb\FileRepo\Implementations\Pdo;

use Gb\FileRepo\Implementations\Traits\Hydrate;
use Gb\FileRepo\Model\File;
use Gb\FileRepo\Model\File\FileId;
use Gb\FileRepo\Model\File\UniqueKey;
use Gb\FileRepo\Repository\FileRepositoryInterface;
use PDO;

class FileRepository implements FileRepositoryInterface
{
    use Hydrate;

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
        'storage_id',
        'upload_arguments',
        'unique_key'
    ];

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findByKey(UniqueKey $key): ?File
    {
        $sql = <<<EOSQL
SELECT %s FROM gb_file WHERE unique_key=:key LIMIT 1
EOSQL;

        $stmt = $this->connection->prepare(sprintf($sql, implode(',', $this->columns)));
        $stmt->execute([
            ':key' => $key
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

        $stmt = $this->connection->prepare(sprintf($sql, implode(',', $this->columns)));
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
}
