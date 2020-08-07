<?php

namespace Gb\FileRepo\Implementations\File;

use Gb\FileRepo\Implementations\Traits\Hydrate;
use Gb\FileRepo\Model\File;
use Gb\FileRepo\Model\File\FileId;
use Gb\FileRepo\Repository\FileRepositoryInterface;

class JsonRepository implements FileRepositoryInterface
{
    use Hydrate;

    private string $filePath;
    private array $data;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        if (file_exists($filePath)) {
            $this->data = json_decode(file_get_contents($filePath), true);
            if ($this->data === false) {
                throw new \RuntimeException("Invalid storage file");
            }
        } else {
            $this->data = [];
        }
    }

    public function findBySizeAndChecksum(int $size, string $checksum): ?File
    {
        foreach ($this->data as $row) {
            if ($row['size'] === $size && $row['checksum'] === $checksum) {
                return $this->hydrate($row);
            }
        }

        return null;
    }

    public function save(File $file): void
    {
        $this->data[] = $this->toArray($file);
        $this->persist();
    }

    public function find(FileId $fileId): ?File
    {
        foreach ($this->data as $row) {
            if ($row['file_id'] === $fileId->toString()) {
                return $this->hydrate($row);
            }
        }

        return null;
    }

    public function remove(FileId $fileId): void
    {
        foreach ($this->data as $index => $row) {
            if ($row['file_id'] === $fileId->toString()) {
                unset($this->data[$index]);
                $this->persist();
                return;
            }
        }
    }

    private function persist(): void
    {
        file_put_contents($this->filePath, json_encode($this->data));
    }
}
