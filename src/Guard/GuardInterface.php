<?php

declare(strict_types=1);

namespace Gb\FileRepo\Guard;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface GuardInterface
{
    public function check(UploadedFile $uploadedFile): CheckResult;
}