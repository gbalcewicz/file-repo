<?php

namespace Gb\Example\Event;

use Assert\Assertion;
use Prooph\Common\Messaging\DomainEvent;

final class QuickStartSucceeded extends DomainEvent
{
    /**
     * @var string
     */
    private $text;

    public static function withSuccessMessage(string $text): QuickStartSucceeded
    {
        return new self($text);
    }

    private function __construct(string $text)
    {
        Assertion::minLength($text, 1, 'Success message must be at least 1 char long');
        $this->text = $text;
        $this->metadata['_aggregate_version'] = 1;
        $this->init();
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function payload(): array
    {
        return ['text' => $this->text];
    }

    protected function setPayload(array $payload): void
    {
        $this->text = $payload['text'];
    }
}