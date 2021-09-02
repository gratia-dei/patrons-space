<?php

abstract class Base
{
    private $date;
    private $environment;
    private $file;
    private $json;
    private $path;

    protected const GENERATED_FILE_NAME_SUFFIX = '.generated';
    protected const DATA_FILE_EXTENSION = '.json';

    public function __construct()
    {
        $this->date = new Date();
        $this->environment = new Environment();
        $this->file = new File();
        $this->json = new Json();
        $this->path = new Path();
    }

    protected function getDate(): Date
    {
        return $this->date;
    }

    protected function getEnvironment(): Environment
    {
        return $this->environment;
    }

    protected function getFile(): File
    {
        return $this->file;
    }

    protected function getJson(): Json
    {
        return $this->json;
    }

    protected function getPath(): Path
    {
        return $this->path;
    }

    protected function getOriginalJsonFileContentArrayForFullPath(string $jsonFilePath): array
    {
        $content = $this->getFile()->getFileContent($jsonFilePath);

        return $this->getJson()->decode($content);
    }

    protected function getOriginalJsonFileContentArray(string $jsonFileName): array
    {
        $jsonPath = $this->getPath()->getDataPath($jsonFileName);

        return $this->getOriginalJsonFileContentArrayForFullPath($jsonPath);
    }

    protected function getDataFilesSuffix(): string
    {
        return self::DATA_FILE_EXTENSION;
    }

    protected function getGeneratedFilesSuffix(): string
    {
        return self::GENERATED_FILE_NAME_SUFFIX . $this->getDataFilesSuffix();
    }

    protected function getIndexFilePath(string $path, bool $forGeneratedFile = false): string
    {
        return $path . '/index' . ($forGeneratedFile ? self::GENERATED_FILE_NAME_SUFFIX : '') . self::DATA_FILE_EXTENSION;
    }

    protected function getAliasFilePath(string $path, bool $forGeneratedFile = false): string
    {
        return $path . '/alias' . ($forGeneratedFile ? self::GENERATED_FILE_NAME_SUFFIX : '') . self::DATA_FILE_EXTENSION;
    }
}
