<?php

use Gb\FileRepo\Model\File;
use Gb\FileRepo\Model\File\Directory;
use Gb\FileRepo\Model\File\FileId;
use Gb\FileRepo\Model\File\Name;
use Gb\FileRepo\Model\File\Path;
use Gb\FileRepo\Model\File\UploadedFile;
use Gb\FileRepo\Plugin\UploadedFileProcessor;
use Prooph\Common\Event\ActionEvent;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\Common\Messaging\FQCNMessageFactory;
use Prooph\EventSourcing\Aggregate\AggregateRepository;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\EventStore\ActionEventEmitterEventStore;
use Prooph\EventStore\Pdo\MariaDbEventStore;
use Prooph\EventStore\Pdo\PersistenceStrategy\MariaDbSingleStreamStrategy;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;
use Ramsey\Uuid\Uuid;

require_once __DIR__ . "/../vendor/autoload.php";

$emiter = new ProophActionEventEmitter(ActionEventEmitterEventStore::ALL_EVENTS);
$pdo = new \PDO('mysql:host=127.0.0.1;dbname=gbfilerepo', 'dev', 'dev');

$mariaDbEventStore = new MariaDbEventStore(new FQCNMessageFactory(), $pdo, new MariaDbSingleStreamStrategy());
$eventStore = new ActionEventEmitterEventStore($mariaDbEventStore, $emiter);

$eventStore->attach(
    ActionEventEmitterEventStore::EVENT_APPEND_TO, // InMemoryEventStore provides event hooks
    function (ActionEvent $actionEvent): void {
        $recordedEvents = $actionEvent->getParam('streamEvents');

        foreach ($recordedEvents as $recordedEvent) {
            echo sprintf(
                "Event with name %s was recorded. It occurred on %s ///\n\n",
                $recordedEvent->messageName(),
                $recordedEvent->createdAt()->format('Y-m-d H:i:s')
            );
        }
    },
    -1000 // low priority, so after action happened
);

$streamName = new StreamName('file_event_stream');
if (!$eventStore->hasStream($streamName)) {
    $eventStore->create(new Stream($streamName, new ArrayIterator()));
}

$repository = new AggregateRepository(
    $eventStore,
    AggregateType::fromAggregateRootClass(File::class),
    new AggregateTranslator(),
    null,
    $streamName,
    false
);

$eventStore->attach(ActionEventEmitterEventStore::EVENT_APPEND_TO, new UploadedFileProcessor($repository), -1001);

$file = File::upload(
    FileId::fromString(Uuid::uuid4()),
    new UploadedFile(
        Directory::create('/home/grzes/Pictures'),
        Path::fromFilePath('p.jpg'),
        'image/jpeg',
        Name::createFromFullName('oryginalny.plik.jpg')
    )
);

$repository->saveAggregateRoot($file);