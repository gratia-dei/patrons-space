<?php

class DateMyPatronsFullContentBlock extends ContentBlock implements ContentBlockInterface
{
    protected const IMMOVABLE_FILE_PATH = 'generated/dates-my-patrons-immovable';
    protected const MOVABLE_FILE_PATH = 'generated/dates-my-patrons-movable';

    protected $date;
    protected $immovableFileData = [];
    protected $movableFileData = [];
    protected $mainTemplate;
    protected $itemTemplate;

    //protected $dayData;

    final public function prepare(string $date): ContentBlock
    {
        $this->setProperties();

        //$dateWithoutYear = substr($date, 5);

        $this->date = $date;
        $this->itemTemplate = $this->getOriginalHtmlFileContent('items/dates-patron-item.html');

        //$this->dayData = $fileData[$dateWithoutYear] ?? [];

        return $this;
    }

    protected function setProperties(): void
    {
        $immovableFilePath = $this->getGeneratedFileSuffix(self::IMMOVABLE_FILE_PATH);
        $this->immovableFileData = $this->getOriginalJsonFileContentArray($immovableFilePath);

        $movableFilePath = $this->getGeneratedFileSuffix(self::MOVABLE_FILE_PATH);
        $this->movableFileData = $this->getOriginalJsonFileContentArray($movableFilePath);

        $this->mainTemplate = $this->getOriginalHtmlFileContent('content-blocks/date-my-patrons-full-content-block.html');
    }

    final public function getFullContent(string $translatedName): string
    {
        $mainContent = $this->mainTemplate;

        $patronsListContent = self::VARIABLE_NAME_SIGN . 'lang-comming-soon' . self::VARIABLE_NAME_SIGN;
        //$recordIds = array_keys($this->dayData);
        //foreach ($recordIds as $recordId) {
            //$patronsListContent .= $this->getRecordContent($recordId);
        //}

        $variables = [
            'date' => $this->date,
            'patrons-list' => $patronsListContent,
        ];

        return $this->getReplacedContent($mainContent, $variables);
    }

    final public function getRecordContent(string $recordId): string
    {
        //$variables = [
            //'name' => $this->dayData[$recordId],
        //];

        //return $this->getReplacedContent($this->itemContent, $variables);
    }
}
