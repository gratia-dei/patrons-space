<?php

class DateMyPatronsSimplifiedListContentBlock extends DateMyPatronsFullContentBlock implements ContentBlockInterface
{
    protected function setOtherProperties(string $date): void
    {
        $immovableFilePath = $this->getGeneratedFileSuffix(self::IMMOVABLE_FILE_PATH);
        $immovableFileData = $this->getOriginalJsonFileContentArray($immovableFilePath);
        $this->rows = $this->getImmovableRows($date, $immovableFileData);

        $this->mainTemplate = $this->getOriginalHtmlFileContent('content-blocks/date-my-patrons-simplified-list-content-block.html');
    }
}
