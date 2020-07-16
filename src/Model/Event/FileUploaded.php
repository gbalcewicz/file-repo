<?php

namespace Gb\FileRepo\Model\Event;

use Gb\FileRepo\Model\File\FileId;
use Prooph\EventSourcing\AggregateChanged;

class FileUploaded extends AggregateChanged
{
    public function fileId(): FileId
    {
        return FileId::fromString($this->aggregateId());
    }
}