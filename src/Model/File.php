<?php

declare(strict_types=1);

namespace Gb\FileRepo\Model;

use Gb\FileRepo\Model\Event\FileUploaded;
use Gb\FileRepo\Model\File\FileId;
use Gb\FileRepo\Model\File\Path;
use Gb\FileRepo\Model\File\UploadedFile;
use Prooph\EventSourcing\AggregateChanged;
use Prooph\EventSourcing\AggregateRoot;

class File extends AggregateRoot
{
    private ?FileId $fileId;

    private bool $removed = false;

    private bool $ready = false;

    public static function upload(
        FileId $fileId,
        UploadedFile $uploadedFile
    ): self {
        $self = new self();
        $self->recordThat(FileUploaded::occur($fileId->toString(), [
            'path' => $uploadedFile->path()->fullPath(),
            'size' => $uploadedFile->size(),
            'mimeType' => $uploadedFile->mimeType(),
            'originalName' => $uploadedFile->originalName()->toString(),
            'checksum' => $uploadedFile->checksum()
        ]));

        return $self;
    }

    protected function aggregateId(): string
    {
        return $this->fileId->toString();
    }

    protected function apply(AggregateChanged $event): void
    {
        switch ($event->messageName()) {
            case FileUploaded::class:
                /** @var $event FileUploaded */
                $this->fileId = $event->fileId();
            break;

            case FileRemoved::class:
                $this->removed = true;
            break;

            case FileProcessed::class:
                $this->processed = true;
            break;
        }
    }

}