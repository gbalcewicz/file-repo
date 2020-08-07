<?php

namespace Gb\FileRepo\Key;

use Gb\FileRepo\Model\File\UniqueKey;
use Gb\FileRepo\UploadedFile;

interface UniqueKeyGeneratorInterface
{
    public function generateKey(UploadedFile $uploadedFile): UniqueKey;
}
