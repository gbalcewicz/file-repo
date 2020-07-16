<?php

declare(strict_types=1);

namespace Gb\FileRepo\Plugin;

use Gb\FileRepo\Model\Event\FileUploaded;
use Prooph\Common\Event\ActionEvent;
use Prooph\EventSourcing\Aggregate\AggregateRepository;

class UploadedFileProcessor
{
    private AggregateRepository $repository;

    public function __construct(AggregateRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(ActionEvent $actionEvent)
    {
        $recordedEvents = $actionEvent->getParam('streamEvents');

        foreach ($recordedEvents as $recordedEvent) {
            if ($recordedEvent->messageName() !== FileUploaded::class) {
                continue;
            }
            $file = $this->repository->getAggregateRoot($recordedEvent->aggregateId());

            var_dump($recordedEvent);
        }
    }
}