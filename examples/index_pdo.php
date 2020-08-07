<?php

use Gb\FileRepo\Guard\AlwaysAcceptingGuard;
use Gb\FileRepo\Implementations\Pdo\FileRepository;
use Gb\FileRepo\Key\SizeAndMd5ChecksumUniqueKey;
use Gb\FileRepo\Message\ProcessFile;
use Gb\FileRepo\Storage\RegistryStorage;
use Gb\FileRepo\UploadedFile;
use Gb\FileRepo\Uploader;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

require_once __DIR__ . "/../vendor/autoload.php";

$bus = new MessageBus([
    new HandleMessageMiddleware(new HandlersLocator([
        ProcessFile::class => [function (ProcessFile $processFile) {
            echo sprintf("Processing file %s\n", $processFile->fileId()->toString());
        }]
    ]))
]);

$pdo = new \PDO('mysql:host=127.0.0.1;dbname=gbfilerepo', 'dev', 'dev');
$repository = new FileRepository($pdo);
$storage = new RegistryStorage($repository, new SizeAndMd5ChecksumUniqueKey(), '/tmp/xxx');
$uploader = new Uploader($bus, new AlwaysAcceptingGuard(), $storage);
file_put_contents('/tmp/file.txt', sprintf('[%s] something', date('Y-m-d H:i:s')), FILE_APPEND);
$uploadedFile = new SymfonyUploadedFile('/tmp/file.txt', 'file.txt', 'text/plain', null, true);

$uploader->uploadFile(new UploadedFile($uploadedFile));
