<?php

class MainContentForDirectories extends Content implements MainContentInterface
{
    private $indexData;
    private $path;

    public function configure(string $path): bool
    {
        if ($path === '') {
            return false;
        } else if ($path === self::DATA_ROOT_PARENT_DIRECTORY_PATH) {
            $path = '';
        }

        $path .= '/';
        $indexFilePath = $this->getIndexFilePath($path);
        $indexData = $this->getOriginalJsonFileContentArray($indexFilePath);
        if (empty($indexData)) {
            return false;
        }

        $this->path = $path;
        $this->indexData = $indexData;

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
        $language = $this->getLanguage();

        $originalContent = $this->getOriginalHtmlFileContent('main-content-for-directories.html');
        $itemContent = $this->getOriginalHtmlFileContent('directory-list-item.html');

        $listContent = '';
        foreach ($indexData as $variableName => $itemNames) {
            $itemVariables = [
                'href' => $path . $variableName,
                'name' => self::VARIABLE_NAME_SIGN . $variableName . self::VARIABLE_NAME_SIGN,
            ];
            $listContent .= $this->getReplacedContent($itemContent, $itemVariables);
        }

        $indexFilePath = $this->getIndexFIlePath($path);
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
