<?php

class RomanMartyrology2004MovableFeastsContentBlock extends ContentBlock implements ContentBlockInterface
{
    private const PAGE_INDEX = 'page';
    private const BASE_INDEX = 'base';
    private const MOVE_INDEX = 'move';

    private const VAR_PREFIX = 'record-text-';

    private $recordContent;
    private $textVariables;

    public function prepare(string $path): ContentBlock
    {
        $recordContent = $this->getOriginalHtmlFileContent('items/roman-martyrology-2004-movable-feast-item.html');

        $this->prapareConsolidatedDataFilesArray($path);

        $translations = $this->getRecordTranslations(
            $this->getMainFileData(),
            $this->getDataLinksFileData()
        );
        $language = $this->getLanguage();
        $textVariables = $this->getTranslatedVariablesForLangData($language, $translations);

        $this->recordContent = $recordContent;
        $this->textVariables = $textVariables;

        return $this;
    }

    public function getFullContent(string $translatedName): string
    {
        $contentBlockContent = $this->getOriginalHtmlFileContent('content-blocks/roman-martyrology-2004-movable-feasts-content-block.html');
        $pageHeaderContent = $this->getOriginalHtmlFileContent('items/page-header-item.html');
        $mainFileData = $this->getMainFileData();

        $prevPageNumber = null;
        $pageNumber = self::UNKNOWN_PAGE_NUMBER;

        $recordsContent = '';
        foreach ($mainFileData as $recordId => $recordData) {
            $page = $recordData[self::PAGE_INDEX] ?? null;

            if (!is_null($page)) {
                $pageNumber = $page;
            }

            if ($prevPageNumber !== $pageNumber) {
                $variables = [
                    'page-number' => $pageNumber,
                ];
                $recordsContent .= $this->getReplacedContent($pageHeaderContent, $variables);
            }

            $recordsContent .= $this->getRecordContent($recordId);

            $prevPageNumber = $pageNumber;
        }

        $variables = [
            'movable-feasts-title' => $translatedName,
            'movable-feasts-items-content' => $recordsContent,
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
            unset($values[self::BASE_INDEX]);
            unset($values[self::MOVE_INDEX]);

            foreach ($values as $language => $text) {
                $result[self::VAR_PREFIX . $key][$language] = $this->getTextWithSpecialLinks($text, $aliases[$key] ?? []);
            }
        }

        return $result;
    }
}
