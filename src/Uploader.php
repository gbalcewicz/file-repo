<?php

declare(strict_types=1);

namespace Gb\FileRepo;

use Gb\FileRepo\Guard\CheckResult;
use Gb\FileRepo\Guard\GuardInterface;
use Gb\FileRepo\Message\ProcessFile;
use Gb\FileRepo\Model\File;
use Gb\FileRepo\Storage\StorageInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class Uploader
{
    private MessageBusInterface $messageBus;
    private GuardInterface $guard;
    private StorageInterface $storageInterface;

    public function __construct(
        MessageBusInterface $messageBus,
        GuardInterface $guard,
        StorageInterface $storageInterface
    ) {
        $this->messageBus = $messageBus;
        $this->guard = $guard;
        $this->storageInterface = $storageInterface;
    }

    public function uploadFile(UploadedFile $uploadedFile, array $arguments = []): ?File
    {
        if ($this->guard->check($uploadedFile)->equals(CheckResult::rejected())) {
            return null;
        }

        $file = $this->storageInterface->findFile($uploadedFile);
        if ($file instanceof File) {
            return $file;
        }

        $file = $this->storageInterface->storeFile($uploadedFile);
        $this->messageBus->dispatch(new ProcessFile($file->fileId()));

        return $file;
    }
}
