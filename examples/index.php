<?php

use Gb\FileRepo\Guard\AlwaysAcceptingGuard;
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

$pdo = new \PDO('mysql:host=127.0.0.1;dbname=gbfilerepo', 'dev', 'dev');
$repository = new FileRepository($pdo);
$storage = new RegistryStorage($repository, '/tmp/xxx');
$uploader = new Uploader($bus, new AlwaysAcceptingGuard(), $storage);
$uploadedFile = new UploadedFile('/home/grzes/Pictures/p.jpg', 'p.jpg', 'image/jpeg', null, true);

$uploader->uploadFile($uploadedFile);