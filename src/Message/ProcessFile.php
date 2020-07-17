<?php

declare(strict_types=1);

namespace Gb\FileRepo\Message;

use Gb\FileRepo\Model\File\FileId;

class ProcessFile
{
    private string $fileId;

    public function __construct(FileId $fileId)
    {
        $this->fileId = $fileId->toString();
    }

    public function fileId(): FileId
    {
        return FileId::fromString($this->fileId);
    }
}