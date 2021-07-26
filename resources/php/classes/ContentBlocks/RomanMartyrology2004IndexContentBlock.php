<?php

class RomanMartyrology2004IndexContentBlock extends ContentBlock implements ContentBlockInterface
{
    private const PAGE_INDEX = 'page';
    private const PAGE_COLUMN_INDEX = 'column';

    private const VAR_PREFIX = 'record-text-';

    public function getContent(string $directoryPath, string $fileName, array $fileData): string
    {
        $result = '';

        $pageHeaderContent = $this->getOriginalHtmlFileContent('items/page-header-with-column-item.html');
        $indexItemContent = $this->getOriginalHtmlFileContent('items/roman-martyrology-2004-index-item.html');

        $translations = $this->getRecordTranslations($fileData);
        $language = $this->getLanguage();
        $textVariables = $this->getTranslatedVariablesForLangData($language, $translations);

        $prevPageNumber = null;
        $prevPageColumnNumber = null;
        $pageNumber = self::UNKNOWN_PAGE_NUMBER;
        $pageColumnNumber = self::UNKNOWN_PAGE_COLUMN_NUMBER;

        foreach ($fileData as $recordId => $recordData) {
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
                $result .= $this->getReplacedContent($pageHeaderContent, $variables);
            }

            $variables = [
                'record-id' => $recordId,
                'record-text' => self::VARIABLE_NAME_SIGN . self::VAR_PREFIX . $recordId . self::VARIABLE_NAME_SIGN,
            ];
            $result .= $this->getReplacedContent($indexItemContent, $variables);

            $prevPageNumber = $pageNumber;
            $prevPageColumnNumber = $pageColumnNumber;
        }

        return $this->getReplacedContent($result, $textVariables, true);
    }

    private function getRecordTranslations(array $data): array
    {
        $result = [];

        foreach ($data as $key => $values) {
            unset($values[self::PAGE_INDEX]);
            unset($values[self::PAGE_COLUMN_INDEX]);

            $result[self::VAR_PREFIX . $key] = $values;
        }

        return $result;
    }
}
