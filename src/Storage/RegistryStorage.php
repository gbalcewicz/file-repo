<?php

declare(strict_types=1);

namespace Gb\FileRepo\Storage;

use Gb\FileRepo\Key\UniqueKeyGeneratorInterface;
use Gb\FileRepo\Model\File;
use Gb\FileRepo\Model\File\Directory;
use Gb\FileRepo\Model\File\FileId;
use Gb\FileRepo\Model\File\Name;
use Gb\FileRepo\Model\File\Path;
use Gb\FileRepo\Repository\FileRepositoryInterface;
use Gb\FileRepo\UploadedFile;
use Ramsey\Uuid\Uuid;

class RegistryStorage implements StorageInterface
{
    private FileRepositoryInterface $fileRepository;
    private Directory $baseDir;
    private UniqueKeyGeneratorInterface $uniqueKeyGenerator;

    public function __construct(
        FileRepositoryInterface $fileRepository,
        UniqueKeyGeneratorInterface $uniqueKeyGenerator,
        string $baseDir
    ) {
        $this->fileRepository = $fileRepository;
        $this->baseDir = Directory::create($baseDir);
        $this->uniqueKeyGenerator = $uniqueKeyGenerator;
    }

    public function id(): StorageId
    {
        return StorageId::fromString(sprintf('reg-%s', sha1($this->baseDir->toString())));
    }

    public function findFile(UploadedFile $uploadedFile): ?File
    {
        $uniqueKey = $this->uniqueKeyGenerator->generateKey($uploadedFile);

        return $this->fileRepository->findByKey($uniqueKey);
    }

    public function storeFile(UploadedFile $uploadedFile): File
    {
        $rawUploadedFile = $uploadedFile->getWrappedUploadedFile();
        $checksum = md5_file($rawUploadedFile->getRealPath());
        $uuid = Uuid::uuid4()->toString();
        list($timeLow, $timeMid, $timeHigh, $clock, $node) = explode('-', $uuid);
        $location = sprintf('%s/%s', substr($timeHigh, 0, 2), substr($timeHigh, 2, 2));
        $baseName = sprintf('%s-%s-%s-%s', $timeMid, $timeLow, $clock, $node);
        $originalName = Name::createFromFullName($rawUploadedFile->getClientOriginalName());
        $file = new File(
            FileId::fromString($uuid),
            Path::create(Directory::create($location), Name::create($baseName, $originalName->extension())),
            $originalName,
            $rawUploadedFile->getClientMimeType(),
            $rawUploadedFile->getSize(),
            $checksum,
            $this->id(),
            $this->uniqueKeyGenerator->generateKey($uploadedFile),
            $uploadedFile->getArguments()
        );
        $absolutePath = $file->path()->asRelativeTo($this->baseDir);
        $rawUploadedFile->move($absolutePath->dir()->toString(), $absolutePath->name()->toString());
        $this->fileRepository->save($file);

        return $file;
    }

    public function deleteFile(File $file): void
    {
        $this->fileRepository->remove($file->fileId());
        $path = $this->localPath($file)->fullPath();
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function localPath(File $file): Path
    {
        return $file->path()->asRelativeTo($this->baseDir);
    }
}
