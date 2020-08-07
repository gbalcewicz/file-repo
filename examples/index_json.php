<?php

use Gb\FileRepo\Guard\AlwaysAcceptingGuard;
use Gb\FileRepo\Implementations\File\JsonRepository;
use Gb\FileRepo\Implementations\Pdo\FileRepository;
use Gb\FileRepo\Message\ProcessFile;
use Gb\FileRepo\Storage\RegistryStorage;
use Gb\FileRepo\Uploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

$repository = new JsonRepository('/tmp/repo.json');
$storage = new RegistryStorage($repository, '/tmp/xxx');
$uploader = new Uploader($bus, new AlwaysAcceptingGuard(), $storage);
file_put_contents('/tmp/file.txt', sprintf('[%s] something', date('Y-m-d H:i:s')), FILE_APPEND);
$uploadedFile = new UploadedFile('/tmp/file.txt', 'file.txt', 'text/plain', null, true);

$uploader->uploadFile($uploadedFile);
