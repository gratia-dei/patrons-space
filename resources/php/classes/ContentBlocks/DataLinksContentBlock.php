<?php

class DataLinksContentBlock extends ContentBlock implements ContentBlockInterface
{
    private const DATA_LINKS_FIELD_NAME = 'data-links';

    private const LIST_PATH_NAME = '';
    private const RECORD_PATH_NAME = self::VARIABLE_NAME_SIGN . 'lang-resource' . self::MODIFIER_SEPARATOR . self::MODIFIER_CAPITALIZE . self::VARIABLE_NAME_SIGN;
    private const RECORD_NAME = self::VARIABLE_NAME_SIGN . 'lang-position' . self::MODIFIER_SEPARATOR . self::MODIFIER_CAPITALIZE . self::VARIABLE_NAME_SIGN;

    private $data = [];
    private $pathData = [];
    private $breadcrumbsContentBlock;

    public function __construct()
    {
        parent::__construct();

        $this->breadcrumbsContentBlock = new BreadcrumbsContentBlock();
    }

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
        $foundAnyRecord = false;

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
                if (
                    strpos($mainPath, $routingPath) === 0
                    || $mainPath . '/' === $routingPath
                ) {
                    $contentBlockClass = $classForPath;
                    break;
                }
            }

            $listName = self::LIST_PATH_NAME;
            $listTitle = $this->getTranslatedNameForPath($listName, $mainPath);
            $listContent = '';

            if (is_null($contentBlockClass)) {
                continue;
            }

            $previousLinks = [];
            foreach ($aliasData as $link) {
                $recordNumber++;

                $linkData = $this->getDataLinkElements($link);
                if (is_null($linkData)) {
                    continue;
                }
                list($linkId, $subPathAlias, $recordId) = $linkData;
                if (isset($previousLinks[$subPathAlias][$recordId])) {
                    continue;
                }
                $previousLinks[$subPathAlias][$recordId] = true;

                if($subPathAlias === '') {
                    $subPathAlias = basename($mainPath);
                    $mainPath = dirname($mainPath) . '/';
                }

                $fullPath = $this->getPathToRedirect($mainPath . $subPathAlias);
                if ($fullPath === '') {
                    $fullPath = $mainPath . $subPathAlias;
                }

                $recordName = self::RECORD_PATH_NAME;
                $recordTitle = $this->getTranslatedNameForPath($recordName, $fullPath, $mainPath);
                $recordIdLink = $this->getActiveBreadcrumbsLink($fullPath, $recordId);
                $recordContent = (new $contentBlockClass())->prepare($fullPath)->getRecordContent($recordId);

                $recordContent = preg_replace('/ id="([0-9]+)"/U', ' id="record_' . $recordNumber . '"', $recordContent);
                $recordContent = str_replace(self::RECORD_ACTIVENESS_CLASS_ACTIVE, self::RECORD_ACTIVENESS_CLASS_INACTIVE, $recordContent);

                $variables = [
                    'record-title' => "$recordTitle | " . self::RECORD_NAME . ": $recordIdLink",
                    'record-content' => $recordContent,
                ];
                $listContent .= $this->getReplacedContent($recordContentItem, $variables);

                $foundAnyRecord = true;
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

        return $foundAnyRecord ? $result : '';
    }

    public function getRecordContent(string $recordId): string
    {
        //no separate records possible for two dimensions list
    }

    private function getPathDataLinks(array $data, string $path)
    {
        $pathElements = $path === '' ? [] : explode('/', trim($path, '/'));
        foreach ($pathElements as $element) {
            $data = $data[$element] ?? [];
        }

        return $data[self::DATA_LINKS_FIELD_NAME] ?? [];
    }

    private function getTranslatedNameForPath(string $name, string $fullPath, string $contextPath = self::DATA_ROOT_PARENT_DIRECTORY_PATH): string
    {
        $bcPath = $this->breadcrumbsContentBlock->getPathWithContext($fullPath, $contextPath);

        return $this->breadcrumbsContentBlock
            ->prepare($bcPath)
            ->getFullContent($name)
        ;
    }

    private function getActiveBreadcrumbsLink(string $path, string $recordId): string
    {
        return $this->breadcrumbsContentBlock->getLinkWithAnchor($path, $recordId);
    }
}
