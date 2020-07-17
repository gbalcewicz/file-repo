<?php

namespace Gb\FileRepo\Model;

use Gb\FileRepo\Model\Event\FileUploaded;
use Gb\FileRepo\Model\Event\RepositoryCreated;
use Gb\FileRepo\Model\File\Directory;
use Gb\FileRepo\Model\File\FileId;
use Gb\FileRepo\Model\File\Name;
use Gb\FileRepo\Model\File\Path;
use Gb\FileRepo\Model\Repository\RepositoryId;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;

class Repository extends AggregateRoot
{
    private ?RepositoryId $repositoryId;
    private ?Directory $baseDir;

    public static function create(RepositoryId $repositoryId, Directory $baseDir)
    {
        $self = new self();
        $self->recordThat(RepositoryCreated::occur($repositoryId, [
            'baseDir' => $baseDir->toString()
        ]));

        return $self;
    }

    public function uploadFile(FileId $fileId, Path $path, Name $originalName, string $mimeType = null): void
    {
        $absolutePath = $path->asRelativeTo($this->baseDir);
        if (!file_exists($absolutePath->fullPath())) {
            throw new \InvalidArgumentException("File does not exist");
        }

        $size = filesize($absolutePath->fullPath());
        $checksum = md5_file($absolutePath->fullPath());

        $this->recordThat(FileUploaded::occur($fileId->toString(), [
            'path' => $path->fullPath(),
            'size' => $size,
            'mimeType' => $mimeType,
            'originalName' => $originalName->toString(),
            'checksum' => $checksum
        ]));
    }

    protected function aggregateId(): string
    {
        return $this->repositoryId->toString();
    }

    protected function apply(AggregateChanged $event): void
    {
        switch ($event->messageName())
        {
            case RepositoryCreated::class:
                /** @var $event RepositoryCreated */
                $this->repositoryId = $event->repositoryId();
                $this->baseDir = $event->baseDir();
                break;

            case FileUploaded::class:
                /** @var $event FileUploaded */
                break;
        }
    }
}