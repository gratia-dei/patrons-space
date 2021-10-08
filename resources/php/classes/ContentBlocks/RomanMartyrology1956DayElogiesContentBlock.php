<?php

class RomanMartyrology1956DayElogiesContentBlock extends ContentBlock implements ContentBlockInterface
{
    private const PAGE_INDEX = 'page';

    private const VAR_PREFIX = 'record-text-';
    private const VAR_FIRST_CHARACTER_ONLY_SUFFIX = '-first-character-only';
    private const VAR_WITHOUT_FIRST_CHARACTER_SUFFIX = '-without-first-character';

    private $importantRecordContent;
    private $normalRecordContent;
    private $fileData;
    private $generatedFileData;
    private $textVariables;

    public function prepare(string $path): ContentBlock
    {
        $importantRecordContent = $this->getOriginalHtmlFileContent('items/roman-martyrology-1956-day-elogy-important-item.html');
        $normalRecordContent = $this->getOriginalHtmlFileContent('items/roman-martyrology-1956-day-elogy-normal-item.html');

        $filePath = $this->getDataFileSuffix($path);
        $fileData = $this->getOriginalJsonFileContentArray($filePath);

        $generatedFilePath = $this->getGeneratedFileSuffix($path);
        $generatedFileData = $this->getOriginalJsonFileContentArray($generatedFilePath);

        $translations = $this->getRecordTranslations($fileData);
        $language = $this->getLanguage();
        $textVariables = $this->getTranslatedVariablesForLangData($language, $translations);

        $this->importantRecordContent = $importantRecordContent;
        $this->normalRecordContent = $normalRecordContent;
        $this->fileData = $fileData;
        $this->generatedFileData = $generatedFileData;
        $this->textVariables = $textVariables;

        return $this;
    }

    public function getFullContent(string $translatedName): string
    {
        $contentBlockContent = $this->getOriginalHtmlFileContent('content-blocks/roman-martyrology-1956-day-elogies-content-block.html');
        $pageHeaderContent = $this->getOriginalHtmlFileContent('items/page-header-item.html');

        $prevPageNumber = null;
        $pageNumber = self::UNKNOWN_PAGE_NUMBER;

        $elogiesContent = '';
        foreach ($this->fileData as $recordId => $recordData) {
            $page = $recordData[self::PAGE_INDEX] ?? null;

            if (!is_null($page)) {
                $pageNumber = $page;
            }
            if ($prevPageNumber !== $pageNumber) {
                $variables = [
                    'page-number' => $pageNumber,
                ];
                $elogiesContent .= $this->getReplacedContent($pageHeaderContent, $variables);
            }

            $elogiesContent .= $this->getRecordContent($recordId, true);

            $prevPageNumber = $pageNumber;
        }

        $dayHeader = $translatedName;
        $variables = [
            'day-header' => $dayHeader,
            'elogies-content' => $elogiesContent,
        ];
        $result = $this->getReplacedContent($contentBlockContent, $variables);

        return $this->getReplacedContent($result, $this->textVariables, true);
    }

    public function getRecordContent(string $recordId, bool $useContextOptions = false): string
    {
        $variables = [
            'record-id' => $recordId,
            'record-text' => self::VARIABLE_NAME_SIGN . self::VAR_PREFIX . $recordId . self::VARIABLE_NAME_SIGN,
            'record-text-first-character-only' => self::VARIABLE_NAME_SIGN . self::VAR_PREFIX . $recordId . self::VAR_FIRST_CHARACTER_ONLY_SUFFIX . self::VARIABLE_NAME_SIGN,
            'record-text-without-first-character' => self::VARIABLE_NAME_SIGN . self::VAR_PREFIX . $recordId . self::VAR_WITHOUT_FIRST_CHARACTER_SUFFIX . self::VARIABLE_NAME_SIGN,
            'record-activeness-class' => $this->getRecordActivenessClass($recordId, $useContextOptions),
        ];

        $isRecordImportant = ($recordId === '1');
        if ($isRecordImportant) {
            $result = $this->getReplacedContent($this->importantRecordContent, $variables);
        } else {
            $result = $this->getReplacedContent($this->normalRecordContent, $variables);
        }

        return $this->getReplacedContent($result, $this->textVariables, true);
    }

    private function getRecordTranslations(array $data): array
    {
        $result = [];

        $aliases = []; //...todo numeric links data from generatedFileData

        foreach ($data as $key => $values) {
            unset($values[self::PAGE_INDEX]);

            $result[self::VAR_PREFIX . $key] = $values;
            foreach ($values as $language => $text) {
                $text = $this->getTextWithSpecialLinks($text, $aliases);

                $result[self::VAR_PREFIX . $key][$language] = $text;
                $result[self::VAR_PREFIX . $key . self::VAR_FIRST_CHARACTER_ONLY_SUFFIX][$language] = mb_substr($text, 0, 1, self::ENCODING);
                $result[self::VAR_PREFIX . $key . self::VAR_WITHOUT_FIRST_CHARACTER_SUFFIX][$language] = mb_substr($text, 1, null, self::ENCODING);
            }
        }

        return $result;
    }
}
