<?php

declare(strict_types=1);

namespace Gb\FileRepo\Model\Event;

use Gb\FileRepo\Model\File\Directory;
use Gb\FileRepo\Model\Repository\Quota;
use Gb\FileRepo\Model\Repository\RepositoryId;
use Prooph\EventSourcing\AggregateChanged;

class RepositoryCreated extends AggregateChanged
{
    public function repositoryId(): RepositoryId
    {
        return RepositoryId::fromString($this->aggregateId());
    }

    public function baseDir(): Directory
    {
        return Directory::create($this->payload['baseDir'] ?? '.');
    }
}