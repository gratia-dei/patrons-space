<?php

class DataLinksContentBlock extends ContentBlock implements ContentBlockInterface
{
    private const DATA_LINKS_FIELD_NAME = 'data-links';

    private $data = [];
    private $pathData = [];

    public function setData(array $data): ContentBlock
    {
        $this->data = $data;

        return $this;
    }

    public function prepare(string $path): ContentBlock
    {
        $data = $this->data;
        $this->pathData = $this->getPathDataLinks($data, $path);

        return $this;
    }

    public function getFullContent(string $translatedName): string
    {
        $result = '';

        $contentBlockRouting = $this->getOriginalJsonFileContentArray('data-file-content-block-configuration.json');

        $contentBlockContent = $this->getOriginalHtmlFileContent('content-blocks/data-links-content-block.html');
        $listContentItem = $this->getOriginalHtmlFileContent('items/data-link-list-item.html');
        $recordContentItem = $this->getOriginalHtmlFileContent('items/data-link-record-item.html');

        $recordNumber = 0;
        $tableTitle = $translatedName;
        $tableContent = '';
        foreach ($this->pathData as $aliasPath => $aliasData) {
            $mainPath = $this->getPathToRedirect($aliasPath);

            $contentBlockClass = null;
            foreach ($contentBlockRouting as $routingPath => $classForPath) {
                if (strpos($mainPath, $routingPath) === 0) {
                    $contentBlockClass = $classForPath;
                    break;
                }
            }

            $listTitle = $this->getTranslatedNameForPath($mainPath);
            $listContent = '';

            if (is_null($contentBlockClass)) {
                continue;
            }

            foreach ($aliasData as $link) {
                $recordNumber++;

                $linkData = $this->getDataLinkElements($link);
                if (is_null($linkData)) {
                     continue;
                }

                list($linkId, $subPathAlias, $recordId) = $linkData;

                $fullPath = $this->getPathToRedirect($mainPath . $subPathAlias);

                $recordTitle = $this->getTranslatedNameForPath($fullPath, $mainPath);
                $recordContent = (new $contentBlockClass())->prepare($fullPath)->getRecordContent($recordId);

                $recordContent = preg_replace('/ id="([0-9]+)"/U', ' id="record_' . $recordNumber . '"', $recordContent);
                $recordContent = str_replace(self::RECORD_ACTIVENESS_CLASS_ACTIVE, self::RECORD_ACTIVENESS_CLASS_INACTIVE, $recordContent);

                $variables = [
                    'record-title' => $recordTitle,
                    'record-content' => $recordContent,
                ];
                $listContent .= $this->getReplacedContent($recordContentItem, $variables);
            }

            $variables = [
                'list-title' => $listTitle,
                'list-content' => $listContent,
            ];
            $tableContent .= $this->getReplacedContent($listContentItem, $variables);
        }

        $variables = [
            'table-title' => $tableTitle,
            'table-content' => $tableContent,
        ];
        $result = $this->getReplacedContent($contentBlockContent, $variables);

        return $result;
    }

    public function getRecordContent(string $recordId): string
    {
        //no separate records possible for two dimensions list
    }

    private function getPathDataLinks(array $data, string $path): array
    {
        $pathElements = $path === '' ? [] : explode('/', trim($path, '/'));
        foreach ($pathElements as $element) {
            $data = $data[$element] ?? [];
        }

        return $data[self::DATA_LINKS_FIELD_NAME] ?? [];
    }

    private function getTranslatedNameForPath(string $path, string $rootPath = ''): string
    {
        return "$path";
    }
}
