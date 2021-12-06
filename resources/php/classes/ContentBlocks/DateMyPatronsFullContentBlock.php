<?php

class DateMyPatronsFullContentBlock extends ContentBlock implements ContentBlockInterface
{
    private $date;
    private $dayData;
    private $itemContent;

    public function prepare(string $date): ContentBlock
    {
        $fileData = $this->getOriginalJsonFileContentArray('my-top-patrons' . self::DATA_FILE_EXTENSION);

        $dateWithoutYear = substr($date, 5);

        $this->date = $date;
        $this->dayData = $this->getArrayIndexedFrom1($fileData[$dateWithoutYear] ?? []);
        $this->itemContent = $this->getOriginalHtmlFileContent('items/simple-list-item.html');

        return $this;
    }

    public function getFullContent(string $translatedName): string
    {
        $contentBlockContent = $this->getOriginalHtmlFileContent('content-blocks/date-my-patrons-full-content-block.html');

        $patronsListContent = '';
        $recordIds = array_keys($this->dayData);
        foreach ($recordIds as $recordId) {
            $patronsListContent .= $this->getRecordContent($recordId);
        }

        $variables = [
            'date' => $this->date,
            'top-patrons-list' => $patronsListContent,
        ];

        return $this->getReplacedContent($contentBlockContent, $variables);
    }

    public function getRecordContent(string $recordId): string
    {
        $variables = [
            'name' => $this->dayData[$recordId],
        ];

        return $this->getReplacedContent($this->itemContent, $variables);
    }
}
