<?php

namespace Gb\FileRepo\Key;

use Gb\FileRepo\Model\File\UniqueKey;
use Gb\FileRepo\UploadedFile;

class BaseUniqueKeyGenerator implements UniqueKeyGeneratorInterface
{
    public function generateKey(UploadedFile $uploadedFile): UniqueKey
    {
        $rawUploadedFile = $uploadedFile->getWrappedUploadedFile();
        $checksum = md5_file($rawUploadedFile->getRealPath());

        return UniqueKey::fromString(sha1(sprintf(
            '%s;%s;%s',
            $checksum,
            $rawUploadedFile->getSize(),
            json_encode($uploadedFile->getArguments())
        )));
    }
}
