<?php

class DataFileMainContent extends MainContent implements MainContentInterface
{
    private const DEFAULT_CONTENT_BLOCK_CLASS_NAME = 'OtherContentBlock';

    private $path;
    private $fileNameTranslated;
    private $fileData;
    private $generatedFileData;

    public function configure(string $path): bool
    {
        $directoryPath = $this->getDataParentDirectoryPath($path);

        $filePath = $path . self::DATA_FILE_EXTENSION;
        $generatedFilePath = $path . self::GENERATED_FILE_NAME_SUFFIX . self::DATA_FILE_EXTENSION;

        if (in_array($filePath, [
            $this->getIndexFilePath($directoryPath),
            $this->getAliasFilePath($directoryPath),
        ])) {
            return false;
        }

        $fileData = $this->getOriginalJsonFileContentArray($filePath);
        $generatedFileData = $this->getOriginalJsonFileContentArray($generatedFilePath);
        if (!$this->dataPathExists($filePath) && !$this->dataPathExists($generatedFilePath)) {
            return false;
        }

        $language = $this->getLanguage();
        $indexFilePath = $this->getIndexFilePath($directoryPath . '/');
        $indexVariables = $this->getTranslatedVariables($language, $indexFilePath);
        if (empty($indexVariables)) {
            $indexFilePath = $this->getIndexFilePath($directoryPath . '/', true);
            $indexVariables = $this->getTranslatedVariables($language, $indexFilePath);
        }

        $this->path = $path;
        $this->fileNameTranslated = $this->getFileNameTranslated($path, $indexVariables);
        $this->fileData = $fileData;
        $this->generatedFileData = $generatedFileData;

        return true;
    }

    public function getTitle(string $prefix): string
    {
        return $prefix . ': ' . $this->fileNameTranslated;
    }

    public function getContent(): string
    {
        $originalContent = $this->getOriginalHtmlFileContent('main-contents/data-file-main-content.html');

        $directoryPath = $this->getDataParentDirectoryPath($this->path);
        $variables = [
            'file-name' => $this->fileNameTranslated,
            'parent-directory' => $this->getFullResourcePath($directoryPath),
            'content' => $this->getDataFileContent(),
        ];
        $replacedContent = $this->getReplacedContent($originalContent, $variables);

        return $replacedContent;
    }

    private function getFileNameTranslated(string $path, array $indexVariables): string
    {
        $fileNameVariable = self::VARIABLE_NAME_SIGN . basename($path) . self::VARIABLE_NAME_SIGN;
        $translatedFileName = $this->getReplacedContent($fileNameVariable, $indexVariables, true);

        return $translatedFileName;
    }

    private function getDataFileContent(): string
    {
        $path = $this->path;
        $fileNameTranslated = $this->fileNameTranslated;

        $directoryPath = $this->getDataParentDirectoryPath($path);
        $class = self::DEFAULT_CONTENT_BLOCK_CLASS_NAME;
        $contentBlockRouting = $this->getOriginalJsonFileContentArray('data-file-content-block-configuration.json');
        foreach ($contentBlockRouting as $routingPath => $classForPath) {
            if (strpos($directoryPath, $routingPath) === 0) {
                $class = $classForPath;
                break;
            }
        }

        return (new $class())->getContent($path, $fileNameTranslated);
    }
}
