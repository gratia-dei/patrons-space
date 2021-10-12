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

    protected function getDataFileSuffix(string $path = ''): string
    {
        return $path . self::DATA_FILE_EXTENSION;
    }

    protected function getGeneratedFileSuffix(string $path = ''): string
    {
        return $path . self::GENERATED_FILE_NAME_SUFFIX . $this->getDataFileSuffix();
    }

    protected function getIndexFilePath(string $path, bool $forGeneratedFile = false): string
    {
        return $path . '/index' . ($forGeneratedFile ? self::GENERATED_FILE_NAME_SUFFIX : '') . self::DATA_FILE_EXTENSION;
    }

    protected function getAliasFilePath(string $path, bool $forGeneratedFile = false): string
    {
        return $path . '/alias' . ($forGeneratedFile ? self::GENERATED_FILE_NAME_SUFFIX : '') . self::DATA_FILE_EXTENSION;
    }

    protected function getPathToRedirect(string $path): string
    {
        if ($this->dataPathExists($path) || $this->dataPathExists($path . self::DATA_FILE_EXTENSION)) {
            return '';
        }

        $wasPathChanged = false;
        $pathElements = explode('/', $path);
        $pathCount = count($pathElements);
        for ($element = 1; $element <= $pathCount; $element++) {
            $tmpPath = implode('/', array_slice($pathElements, 0, $element));
            $basename = $pathElements[$element - 1];

            if (!$this->dataPathExists($tmpPath)) {
                $aliasFilePath = $this->getAliasFilePath(dirname($tmpPath));
                if (!$this->dataPathExists($aliasFilePath)) {
                    $aliasFilePath = $this->getAliasFilePath(dirname($tmpPath), true);
                    if (!$this->dataPathExists($aliasFilePath)) {
                        break;
                    }
                }

                $aliasData = $this->getOriginalJsonFileContentArray($aliasFilePath);
                if (!isset($aliasData[$basename])) {
                    break;
                }

                if ($basename !== $aliasData[$basename]) {
                    $pathElements[$element - 1] = $aliasData[$basename];
                    $wasPathChanged = true;
                }
            }
        }

        if ($wasPathChanged) {
            $path = implode('/', $pathElements);
            if ($this->dataPathExists($path) || $this->dataPathExists($path . self::DATA_FILE_EXTENSION)) {
                return preg_replace('~[/]+~', '/', '/' . $path);
            }
        }

        return '';
    }

    protected function dataPathExists(string $path): bool
    {
        $dataPath = $this->getPath()->getDataPath($path);

        return $this->getFile()->exists($dataPath);
    }

    protected function getDataLinkElements(string $link): ?array
    {
        if (!preg_match("/^(?'link_id'[1-9][0-9]*)[:](?'path'[^# ]+)[#](?'record_id'[1-9][0-9]*)$/", $link, $matches)) {
            return null;
        }

        $linkId = (int) $matches['link_id'];
        $path = $matches['path'];
        $recordId = (int) $matches['record_id'];

        return [$linkId, $path, $recordId];
    }
}
