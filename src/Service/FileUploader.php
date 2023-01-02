<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class FileUploader {
    private $targetDirectory;

    public function __construct($targetDirectory, private SluggerInterface $slugger, private Filesystem $filesystem)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file, $subDirectory)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getTargetDirectory().'/'.$subDirectory, $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    public function delete(string $fileName, string $subDirectory) {
        $this->filesystem->remove($this->getTargetDirectory().'/'.$subDirectory.'/'.$fileName);
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

}