<?php

namespace KlockTecnologia\KlockHelpers\Models;


use Illuminate\Http\UploadedFile;


class Media
{
    protected $uploadedFile;
    protected $mediaCollection;
    protected $fileName;
    protected $filePath;
    protected $filePrefix;
    protected $generatedFileName;
    protected $diskName;


    public function __construct(UploadedFile $uploadedFile, String $mediaCollection = null, String $filePrefix = null, String $fileName = null, String $filePath = null)
    {
        $this->uploadedFile = $uploadedFile;
        $this->mediaCollection = $mediaCollection;
        $this->fileName = $fileName;
        $this->filePath = $filePath ?? '';
        $this->filePrefix = $filePrefix;
        $this->generateFileName();
    }

    protected function generateFilePrefix(): String
    {

        return  $this->filePrefix ?  uniqid($this->filePrefix) : uniqid('file_');
    }

    protected function generateFileName()
    {
        return $this->generatedFileName = $this->getFileName() ? $this->filePath . $this->getFileName() . '.' . $this->uploadedFile->extension()  : $this->filePath . $this->generateFilePrefix() . '.' . $this->uploadedFile->extension();
    }

    public function getMediaCollection()
    {
        return $this->mediaCollection;
    }

    public function getUploadedFile(): UploadedFile
    {

        return $this->uploadedFile;
    }

    public function getFilePath()
    {
        return $this->uploadedFile->path();
    }

    public function getGeneratedFileName()
    {
        return $this->generatedFileName;
    }

    public function getFileName()
    {
        return $this->fileName ?? $this->getGeneratedFileName();
    }
}
