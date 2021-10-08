<?php

class DirectoryMainContent extends MainContent implements MainContentInterface
{
    private $indexData;
    private $path;
    private $usedGeneratedIndexFile;

    public function configure(string $path): bool
    {
        if (trim($path, '/') === '') {
            return false;
        } else if ($path === self::DATA_ROOT_PARENT_DIRECTORY_PATH) {
            $path = '';
        }

        $path .= '/';
        $indexFilePath = $this->getIndexFilePath($path);
        $indexData = $this->getOriginalJsonFileContentArray($indexFilePath);
        $usedGeneratedIndexFile = false;
        if (empty($indexData)) {
            $indexFilePath = $this->getIndexFilePath($path, true);
            $indexData = $this->getOriginalJsonFileContentArray($indexFilePath);
            $usedGeneratedIndexFile = true;
            if (empty($indexData)) {
                return false;
            }
        }

        $this->path = $path;
        $this->indexData = $indexData;
        $this->usedGeneratedIndexFile = $usedGeneratedIndexFile;

        return true;
    }

    public function getTitle(string $prefix): string
    {
        return $prefix . ': ' . $this->path;
    }

    public function getContent(): string
    {
        $indexData = $this->indexData;
        $path = $this->path;
        $usedGeneratedIndexFile = $this->usedGeneratedIndexFile;
        $language = $this->getLanguage();

        $originalContent = $this->getOriginalHtmlFileContent('main-contents/directory-main-content.html');
        $itemContent = $this->getOriginalHtmlFileContent('items/directory-list-item.html');

        $listContent = '';
        foreach ($indexData as $variableName => $itemNames) {
            $itemVariables = [
                'href' => $path . $variableName,
                'name' => self::VARIABLE_NAME_SIGN . $variableName . self::VARIABLE_NAME_SIGN,
            ];
            $listContent .= $this->getReplacedContent($itemContent, $itemVariables);
        }

        $indexFilePath = $this->getIndexFIlePath($path, $usedGeneratedIndexFile);
        $indexVariables = $this->getTranslatedVariables($language, $indexFilePath);
        $translatedListContent = $this->getReplacedContent($listContent, $indexVariables, true);

        $mainContentVariables = [
            'path' => $path . ':',
            'back-href' => $this->getDataParentDirectoryPath($path),
            'list-content' => $translatedListContent,
        ];
        $replacedContent = $this->getReplacedContent($originalContent, $mainContentVariables);

        return $replacedContent;
    }
}
