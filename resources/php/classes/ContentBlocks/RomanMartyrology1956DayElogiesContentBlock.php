<?php

class RomanMartyrology1956DayElogiesContentBlock extends ContentBlock implements ContentBlockInterface
{
    private const PAGE_INDEX = 'page';

    private const VAR_PREFIX = 'record-text-';
    private const VAR_FIRST_CHARACTER_ONLY_SUFFIX = '-first-character-only';
    private const VAR_WITHOUT_FIRST_CHARACTER_SUFFIX = '-without-first-character';

    public function getContent(string $path, string $fileNameTranslated): string
    {
        $contentBlockContent = $this->getOriginalHtmlFileContent('content-blocks/roman-martyrology-1956-day-elogies-content-block.html');
        $pageHeaderContent = $this->getOriginalHtmlFileContent('items/page-header-item.html');
        $importantItemContent = $this->getOriginalHtmlFileContent('items/roman-martyrology-1956-day-elogy-important-item.html');
        $normalItemContent = $this->getOriginalHtmlFileContent('items/roman-martyrology-1956-day-elogy-normal-item.html');

        $filePath = $path . self::DATA_FILE_EXTENSION;
        $fileData = $this->getOriginalJsonFileContentArray($filePath);

        $generatedFilePath = $path . self::GENERATED_FILE_NAME_SUFFIX . self::DATA_FILE_EXTENSION;
        $generatedFileData = $this->getOriginalJsonFileContentArray($generatedFilePath);

        $translations = $this->getRecordTranslations($fileData);
        $language = $this->getLanguage();
        $textVariables = $this->getTranslatedVariablesForLangData($language, $translations);

        $prevPageNumber = null;
        $pageNumber = self::UNKNOWN_PAGE_NUMBER;

        $elogiesContent = '';
        foreach ($fileData as $recordId => $recordData) {
            $page = $recordData[self::PAGE_INDEX] ?? null;
            $isRecordImportant = ($recordId === 1);

            if (!is_null($page)) {
                $pageNumber = $page;
            }
            if ($prevPageNumber !== $pageNumber) {
                $variables = [
                    'page-number' => $pageNumber,
                ];
                $elogiesContent .= $this->getReplacedContent($pageHeaderContent, $variables);
            }

            $variables = [
                'record-id' => $recordId,
                'record-text' => self::VARIABLE_NAME_SIGN . self::VAR_PREFIX . $recordId . self::VARIABLE_NAME_SIGN,
                'record-text-first-character-only' => self::VARIABLE_NAME_SIGN . self::VAR_PREFIX . $recordId . self::VAR_FIRST_CHARACTER_ONLY_SUFFIX . self::VARIABLE_NAME_SIGN,
                'record-text-without-first-character' => self::VARIABLE_NAME_SIGN . self::VAR_PREFIX . $recordId . self::VAR_WITHOUT_FIRST_CHARACTER_SUFFIX . self::VARIABLE_NAME_SIGN,
            ];

            if ($isRecordImportant) {
                $elogiesContent .= $this->getReplacedContent($importantItemContent, $variables);
            } else {
                $elogiesContent .= $this->getReplacedContent($normalItemContent, $variables);
            }

            $prevPageNumber = $pageNumber;
        }

        $dayHeader = $fileNameTranslated;
        $variables = [
            'day-header' => $dayHeader,
            'elogies-content' => $elogiesContent,
        ];
        $result = $this->getReplacedContent($contentBlockContent, $variables);

        return $this->getReplacedContent($result, $textVariables, true);
    }

    private function getRecordTranslations(array $data): array
    {
        $result = [];

        foreach ($data as $key => $values) {
            unset($values[self::PAGE_INDEX]);

            $result[self::VAR_PREFIX . $key] = $values;
            foreach ($values as $language => $text) {
                $result[self::VAR_PREFIX . $key . self::VAR_FIRST_CHARACTER_ONLY_SUFFIX][$language] = mb_substr($text, 0, 1, self::ENCODING);
                $result[self::VAR_PREFIX . $key . self::VAR_WITHOUT_FIRST_CHARACTER_SUFFIX][$language] = mb_substr($text, 1, null, self::ENCODING);
            }
        }

        return $result;
    }
}
