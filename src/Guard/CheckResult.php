<?php

declare(strict_types=1);

namespace Gb\FileRepo\Guard;

final class CheckResult
{
    private const STATE_ACCEPTED = 'accepted';
    private const STATE_REJECTED = 'rejected';

    private string $state;

    private function __construct(string $state)
    {
        $this->state = $state;
    }

    public static function rejected(): self
    {
        return new self(self::STATE_REJECTED);
    }

    public static function accepted(): self
    {
        return new self(self::STATE_ACCEPTED);
    }

    public function equals($other): bool
    {
        if (!$other instanceof self) {
            return false;
        }

        return $this->state === $other->state;
    }
}