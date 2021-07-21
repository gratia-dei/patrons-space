<?php

class DataFileMainContent extends MainContent implements MainContentInterface
{
    private $fileData;
    private $fileName;
    private $directoryPath;

    public function configure(string $path): bool
    {
        $fileData = $this->getOriginalJsonFileContentArray($path . self::DATA_FILE_EXTENSION);
        if (empty($fileData)) {
            return false;
        }

        $directoryPath = $this->getDataParentDirectoryPath($path);

        $language = $this->getLanguage();
        $indexFilePath = $this->getIndexFilePath($directoryPath . '/');
        $indexVariables = $this->getTranslatedVariables($language, $indexFilePath);

        $this->directoryPath = $directoryPath;
        $this->fileData = $fileData;
        $this->fileName = $this->getFileName($path, $indexVariables);

        return true;
    }

    public function getTitle(string $prefix): string
    {
        return $prefix . ': ' . $this->fileName;
    }

    public function getContent(): string
    {
        $originalContent = $this->getOriginalHtmlFileContent('main-contents/data-file-main-content.html');

        $variables = [
            'file-name' => $this->fileName,
            'parent-directory' => $this->getFullResourcePath($this->directoryPath),
            'content' => $this->getDataFileContent(),
        ];
        $replacedContent = $this->getReplacedContent($originalContent, $variables);

        return $replacedContent;
    }

    private function getFileName(string $path, array $indexVariables): string
    {
        $fileNameVariable = self::VARIABLE_NAME_SIGN . basename($path) . self::VARIABLE_NAME_SIGN;
        $translatedFileName = $this->getReplacedContent($fileNameVariable, $indexVariables, true);

        return $translatedFileName;
    }

    private function getDataFileContent(): string
    {
        $class = 'OtherContentBlock';

        //... todo routing

        return (new $class())->getContent();
    }
}
