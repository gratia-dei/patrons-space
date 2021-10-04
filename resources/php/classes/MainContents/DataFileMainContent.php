<?php

class DataFileMainContent extends MainContent implements MainContentInterface
{
    private const DEFAULT_CONTENT_BLOCK_CLASS_NAME = 'OtherContentBlock';

    private $directoryPath;
    private $fileNameTranslated;
    private $fileData;
    private $generatedFileData;

    public function configure(string $path): bool
    {
        $directoryPath = $this->getDataParentDirectoryPath($path);

        $fileBaseName = basename($path) . self::DATA_FILE_EXTENSION;
        $filePath = $directoryPath . '/' . $fileBaseName;

        $generatedFileBaseName = basename($path) . self::GENERATED_FILE_NAME_SUFFIX . self::DATA_FILE_EXTENSION;
        $generatedFilePath = $directoryPath . '/' . $generatedFileBaseName;

        if (in_array($filePath, [
            $this->getIndexFilePath($directoryPath),
            $this->getAliasFilePath($directoryPath),
        ])) {
            return false;
        }

        $fileData = $this->getOriginalJsonFileContentArray($filePath);
        $generatedFileData = $this->getOriginalJsonFileContentArray($generatedFilePath);
        if (empty($fileData) && empty($generatedFileData)) {
            return false;
        }

        $language = $this->getLanguage();
        $indexFilePath = $this->getIndexFilePath($directoryPath . '/');
        $indexVariables = $this->getTranslatedVariables($language, $indexFilePath);
        if (empty($indexVariables)) {
            $indexFilePath = $this->getIndexFilePath($directoryPath . '/', true);
            $indexVariables = $this->getTranslatedVariables($language, $indexFilePath);
        }

        $this->directoryPath = $directoryPath;
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

        $variables = [
            'file-name' => $this->fileNameTranslated,
            'parent-directory' => $this->getFullResourcePath($this->directoryPath),
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
        $directoryPath = $this->directoryPath;
        $fileNameTranslated = $this->fileNameTranslated;
        $fileData = $this->fileData;
        $generatedFileData = $this->generatedFileData;

        $class = self::DEFAULT_CONTENT_BLOCK_CLASS_NAME;
        $contentBlockRouting = $this->getOriginalJsonFileContentArray('data-file-content-block-configuration.json');
        foreach ($contentBlockRouting as $path => $classForPath) {
            if (strpos($directoryPath, $path) === 0) {
                $class = $classForPath;
                break;
            }
        }

        return (new $class())->getContent($directoryPath, $fileNameTranslated, $fileData, $generatedFileData);
    }
}
