<?php

declare(strict_types=1);

namespace Gb\FileRepo\Guard;

use Gb\FileRepo\UploadedFile;

interface GuardInterface
{
    public function check(UploadedFile $uploadedFile): CheckResult;
}
