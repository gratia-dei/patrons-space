<?php

class RomanMartyrologyIndexContentBlock extends ContentBlock implements ContentBlockInterface
{
    private const PAGE_INDEX = 'page';
    private const PAGE_COLUMN_INDEX = 'column';

    private const VAR_PREFIX = 'record-text-';

    private $recordContent;
    private $fileData;
    private $generatedFileData;
    private $textVariables;

    public function prepare(string $path): ContentBlock
    {
        $recordContent = $this->getOriginalHtmlFileContent('items/roman-martyrology-index-item.html');

        $filePath = $this->getDataFileSuffix($path);
        $fileData = $this->getOriginalJsonFileContentArray($filePath);

        $generatedFilePath = $this->getGeneratedFileSuffix($path);
        $generatedFileData = $this->getOriginalJsonFileContentArray($generatedFilePath);

        $translations = $this->getRecordTranslations($fileData, $generatedFileData[self::DATA_LINKS_GENERATED_FILES_INDEX] ?? []);
        $language = $this->getLanguage();
        $textVariables = $this->getTranslatedVariablesForLangData($language, $translations);

        $this->recordContent = $recordContent;
        $this->fileData = $fileData;
        $this->generatedFileData = $generatedFileData;
        $this->textVariables = $textVariables;

        return $this;
    }

    public function getFullContent(string $translatedName): string
    {
        $contentBlockContent = $this->getOriginalHtmlFileContent('content-blocks/roman-martyrology-index-content-block.html');
        $pageHeaderContent = $this->getOriginalHtmlFileContent('items/page-header-with-column-item.html');

        $prevPageNumber = null;
        $prevPageColumnNumber = null;
        $pageNumber = self::UNKNOWN_PAGE_NUMBER;
        $pageColumnNumber = self::UNKNOWN_PAGE_COLUMN_NUMBER;

        $recordsContent = '';
        foreach ($this->fileData as $recordId => $recordData) {
            $page = $recordData[self::PAGE_INDEX] ?? null;
            $pageColumn = $recordData[self::PAGE_COLUMN_INDEX] ?? null;

            if (!is_null($page)) {
                $pageNumber = $page;
            }
            if (!is_null($pageColumn)) {
                $pageColumnNumber = $pageColumn;
            }

            if ($prevPageNumber !== $pageNumber || $prevPageColumnNumber !== $pageColumnNumber) {
                $variables = [
                    'page-number' => $pageNumber,
                    'page-column-number' => $pageColumnNumber,
                ];
                $recordsContent .= $this->getReplacedContent($pageHeaderContent, $variables);
            }

            $recordsContent .= $this->getRecordContent($recordId);

            $prevPageNumber = $pageNumber;
            $prevPageColumnNumber = $pageColumnNumber;
        }

        $variables = [
            'starting-letters' => $translatedName,
            'index-items-content' => $recordsContent,
        ];
        $result = $this->getReplacedContent($contentBlockContent, $variables);

        return $this->getReplacedContent($result, $this->textVariables, true);
    }

    public function getRecordContent(string $recordId): string
    {
        $variables = [
            'record-id' => $recordId,
            'record-text' => self::VARIABLE_NAME_SIGN . self::VAR_PREFIX . $recordId . self::VARIABLE_NAME_SIGN,
            'record-activeness-class' => $this->getRecordActivenessClass($recordId),
        ];
        $content = $this->getReplacedContent($this->recordContent, $variables);

        return $this->getReplacedContent($content, $this->textVariables, true);
    }

    private function getRecordTranslations(array $data, array $aliases): array
    {
        $result = [];

        foreach ($data as $key => $values) {
            unset($values[self::PAGE_INDEX]);
            unset($values[self::PAGE_COLUMN_INDEX]);

            foreach ($values as $language => $text) {
                $result[self::VAR_PREFIX . $key][$language] = $this->getTextWithSpecialLinks($text, $aliases[$key] ?? []);
            }
        }

        return $result;
    }
}
