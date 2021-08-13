<?php

class DataFileMainContent extends MainContent implements MainContentInterface
{
    private const DEFAULT_CONTENT_BLOCK_CLASS_NAME = 'OtherContentBlock';
    private const CONTENT_BLOCK_ROUTING = [
        '/records/patrons-and-feasts/patrons/' => 'PatronContentBlock',
        '/sources/martyrologium-romanum-2004/martyrologium-romanum/' => 'RomanMartyrology2004DayElogiesContentBlock',
        '/sources/martyrologium-romanum-2004/index-nominum-sanctorum-et-beatorum/' => 'RomanMartyrology2004IndexContentBlock',
    ];

    private $fileData;
    private $fileBaseName;
    private $fileNameTranslated;
    private $directoryPath;

    public function configure(string $path): bool
    {
        $directoryPath = $this->getDataParentDirectoryPath($path);
        $fileBaseName = basename($path) . self::DATA_FILE_EXTENSION;
        $fullPath = $directoryPath . '/' . $fileBaseName;

        if (in_array($fullPath, [
            $this->getIndexFilePath($directoryPath),
            $this->getAliasFilePath($directoryPath),
        ])) {
            return false;
        }

        $fileData = $this->getOriginalJsonFileContentArray($fullPath);
        if (empty($fileData)) {
            return false;
        }

        $language = $this->getLanguage();
        $indexFilePath = $this->getIndexFilePath($directoryPath . '/');
        $indexVariables = $this->getTranslatedVariables($language, $indexFilePath);

        $this->directoryPath = $directoryPath;
        $this->fileData = $fileData;
        $this->fileBaseName = $fileBaseName;
        $this->fileNameTranslated = $this->getFileNameTranslated($path, $indexVariables);

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
        $fileBaseName = $this->fileBaseName;
        $fileData = $this->fileData;
        $fileNameTranslated = $this->fileNameTranslated;

        $class = self::DEFAULT_CONTENT_BLOCK_CLASS_NAME;
        foreach (self::CONTENT_BLOCK_ROUTING as $path => $classForPath) {
            if (strpos($directoryPath, $path) === 0) {
                $class = $classForPath;
                break;
            }
        }

        return (new $class())->getContent($directoryPath, $fileBaseName, $fileData, $fileNameTranslated);
    }
}
