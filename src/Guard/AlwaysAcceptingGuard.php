<?php

declare(strict_types=1);

namespace Gb\FileRepo\Guard;

use Gb\FileRepo\UploadedFile;

class AlwaysAcceptingGuard implements GuardInterface
{
    public function check(UploadedFile $uploadedFile): CheckResult
    {
        return CheckResult::accepted();
    }
}
