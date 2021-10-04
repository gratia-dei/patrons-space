<?php

class RomanMartyrology2004DayElogiesContentBlock extends ContentBlock implements ContentBlockInterface
{
    private const PAGE_INDEX = 'page';
    private const MARK_INDEX = 'mark';

    private const IMPORTANT_RECORD_MARK_SIGN = '!';

    private const VAR_PREFIX = 'record-text-';
    private const VAR_FIRST_CHARACTER_ONLY_SUFFIX = '-first-character-only';
    private const VAR_WITHOUT_FIRST_CHARACTER_SUFFIX = '-without-first-character';

    public function getContent(string $path, string $fileNameTranslated): string
    {
        $contentBlockContent = $this->getOriginalHtmlFileContent('content-blocks/roman-martyrology-2004-day-elogies-content-block.html');
        $pageHeaderContent = $this->getOriginalHtmlFileContent('items/page-header-item.html');
        $importantItemContent = $this->getOriginalHtmlFileContent('items/roman-martyrology-2004-day-elogy-important-item.html');
        $normalItemContent = $this->getOriginalHtmlFileContent('items/roman-martyrology-2004-day-elogy-normal-item.html');

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
            $recordType = $recordData[self::MARK_INDEX] ?? '';

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
                'record-type' => $recordType,
                'record-text' => self::VARIABLE_NAME_SIGN . self::VAR_PREFIX . $recordId . self::VARIABLE_NAME_SIGN,
                'record-text-first-character-only' => self::VARIABLE_NAME_SIGN . self::VAR_PREFIX . $recordId . self::VAR_FIRST_CHARACTER_ONLY_SUFFIX . self::VARIABLE_NAME_SIGN,
                'record-text-without-first-character' => self::VARIABLE_NAME_SIGN . self::VAR_PREFIX . $recordId . self::VAR_WITHOUT_FIRST_CHARACTER_SUFFIX . self::VARIABLE_NAME_SIGN,
            ];

            if ($recordType === self::IMPORTANT_RECORD_MARK_SIGN) {
                $elogiesContent .= $this->getReplacedContent($importantItemContent, $variables);
            } else {
                $elogiesContent .= $this->getReplacedContent($normalItemContent, $variables);
            }

            $prevPageNumber = $pageNumber;
        }

        $mainDayName = $fileNameTranslated;
        $romanCalendarDayName = '';
        if (preg_match("/^(?'opentag'<[^>]+>)?(?'main'.+)\s\((?'roman'.+)\)(?'closetag'<\/[^>]+>)?/", $fileNameTranslated, $matches)) {
            $openTag = $matches['opentag'] ?? '';
            $closeTag = $matches['closetag'] ?? '';

            $mainDayName = $openTag . $matches['main'] . $closeTag;
            $romanCalendarDayName = $openTag . $matches['roman'] . $closeTag;
        }
        $variables = [
            'main-day-name' => $mainDayName,
            'roman-calendar-day-name' => $romanCalendarDayName,
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
            unset($values[self::MARK_INDEX]);

            $result[self::VAR_PREFIX . $key] = $values;
            foreach ($values as $language => $text) {
                $result[self::VAR_PREFIX . $key . self::VAR_FIRST_CHARACTER_ONLY_SUFFIX][$language] = mb_substr($text, 0, 1, self::ENCODING);
                $result[self::VAR_PREFIX . $key . self::VAR_WITHOUT_FIRST_CHARACTER_SUFFIX][$language] = mb_substr($text, 1, null, self::ENCODING);
            }
        }

        return $result;
    }
}
