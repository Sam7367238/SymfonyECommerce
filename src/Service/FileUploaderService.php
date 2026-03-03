<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class FileUploaderService
{
    public function __construct(
        private string $targetDirectory,
    ) {
    }

    public function upload(UploadedFile $file): string
    {
        $fileName = uniqid().'.'.$file->guessExtension();

        $file->move($this->getTargetDirectory(), $fileName);

        return $fileName;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
