<?php

class DateMyPatronsFullContentBlock extends ContentBlock implements ContentBlockInterface
{
    private const DATE_SOURCE_LANGUAGE_VARIABLE_PREFIX = 'lang-date-source-';

    protected const IMMOVABLE_FILE_PATH = 'generated/dates-my-patrons-immovable';
    protected const MOVABLE_FILE_PATH = 'generated/dates-my-patrons-movable';

    protected $date;
    protected $rows;
    protected $mainTemplate;
    protected $itemTemplate;
    protected $textVariables;

    final public function prepare(string $date): ContentBlock
    {
        $this->date = $date;
        $this->setOtherProperties($date);
        $this->itemTemplate = $this->getOriginalHtmlFileContent('items/dates-patron-item.html');

        $translations = $this->getRecordTranslations($this->rows);
        $language = $this->getLanguage();
        $this->textVariables = $this->getTranslatedVariablesForLangData($language, $translations);

        return $this;
    }

    protected function setOtherProperties(string $date): void
    {
        $movableFilePath = $this->getGeneratedFileSuffix(self::MOVABLE_FILE_PATH);
        $movableFileData = $this->getOriginalJsonFileContentArray($movableFilePath);
        $movableRows = $this->getMovableRows($date, $movableFileData);

        $immovableFilePath = $this->getGeneratedFileSuffix(self::IMMOVABLE_FILE_PATH);
        $immovableFileData = $this->getOriginalJsonFileContentArray($immovableFilePath);
        $immovableRows = $this->getImmovableRows($date, $immovableFileData);

        $rows = $movableRows;
        foreach ($immovableRows as $patronUrl => $patronData) {
            $rows = $this->addPatronToList($rows, $patronUrl, $patronData);
        }

        $this->rows = $rows;
        $this->mainTemplate = $this->getOriginalHtmlFileContent('content-blocks/date-my-patrons-full-content-block.html');
    }

    final public function getFullContent(string $translatedName): string
    {
        $mainContent = $this->mainTemplate;

        $patronsListContent = '';
        foreach ($this->rows as $patronUrl => $recordData) {
            $patronsListContent .= $this->getRecordContent($patronUrl);
        }

        $variables = [
            'date' => $this->date,
            'patrons-list' => $patronsListContent,
        ];

        return $this->getReplacedContent($mainContent, $variables);
    }

    final public function getRecordContent(string $patronUrl): string
    {
        $textVariables = $this->textVariables;

        $name = self::VARIABLE_NAME_SIGN . $this->getLanguageVariableName($patronUrl) . self::VARIABLE_NAME_SIGN;
        $name = $this->getReplacedContent($name, $textVariables, true);

        $link = '/' . $patronUrl;
        $link = $this->getLinkWithActiveRecordIdForAnchor($link);
        $link = $this->getRecordIdPathWithNameExtension($link, $name);

        $variables = [
            'href' => $link,
            'name' => $name,
            'sources' => $this->getSources($patronUrl),
        ];

        return $this->getReplacedContent($this->itemTemplate, $variables);
    }

    protected function getImmovableRows(string $date, array $fileData): array
    {
        $dateWithoutYear = substr($date, 5);

        return $fileData[$dateWithoutYear] ?? [];
    }

    protected function getMovableRows(string $date, array $fileData): array
    {
        $result = [];

        $year = (int) substr($date, 0, 4);

        $methodResults = [];
        foreach ($fileData as $methodWithMoveDays => $feastData) {
            $methodWithMoveDaysArr = explode('|', $methodWithMoveDays);
            $method = $methodWithMoveDaysArr[0];
            $moveDays = $methodWithMoveDaysArr[1];

            if (!isset($methodResults[$method])) {
                $methodResults[$method] = $this->getMovableFeastBase()->$method($year);
            }
            $methodResult = $methodResults[$method];
            if (!$this->getDate()->isValidMonthWithDay($methodResult)) {
                continue;
            }

            $feastDate = $this->getDate()->getDateMovedByDays("$year-$methodResult", $moveDays);
            if ($feastDate === $date) {
                foreach ($feastData as $patronUrl => $patronData) {
                    $result = $this->addPatronToList($result, $patronUrl, $patronData);
                }
            }
        }

        return $result;
    }

    private function addPatronToList(array $data, string $patronUrl, array $patronData): array
    {
        foreach ($patronData as $index => $indexData) {
            foreach ($indexData as $key => $value) {
                $data[$patronUrl][$index][$key] = $value;
            }
        }

        return $data;
    }

    private function getLanguageVariableName(string $patronUrl): string
    {
        return str_replace(['/', '#'], '-', $patronUrl);
    }

    private function getRecordTranslations(array $data): array
    {
        $result = [];

        foreach ($data as $patronUrl => $recordData) {
            $variableName = $this->getLanguageVariableName($patronUrl);
            $result[$variableName] = $recordData[self::DATES_DATA_PATRON_RECORD_NAME_INDEX] ?? [];
        }

        return $result;
    }

    private function getSources(string $patronUrl): array
    {
        $result = [];

        $sources = array_keys($this->rows[$patronUrl][self::DATES_DATA_PATRON_RECORD_SOURCES_INDEX] ?? []);
        foreach ($sources as $source) {
            $result[] = self::VARIABLE_NAME_SIGN . self::DATE_SOURCE_LANGUAGE_VARIABLE_PREFIX . $source . self::VARIABLE_NAME_SIGN;
        }

        return $result;
    }
}
