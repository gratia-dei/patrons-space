<?php

class PatronContentBlock extends ContentBlock implements ContentBlockInterface
{
    private const NAMES_INDEX = 'names';
    private const TITLES_INDEX = 'titles';

    private const TRANSLATED_INDEXES = [
        self::NAMES_INDEX,
    ];
    private const TITLES_TRANSLATED_INDEXES = [
        self::NAMES_INDEX,
    ];

    private $dataLinksContentBlock;

    private $fileData;
    private $generatedFileData;
    private $textVariables;

    public function __construct()
    {
        $this->dataLinksContentBlock = new DataLinksContentBlock();

        parent::__construct();
    }

    public function prepare(string $path): ContentBlock
    {
        $titleRecordContent = $this->getOriginalHtmlFileContent('items/patron-title-record-item.html');

        $filePath = $this->getDataFileSuffix($path);
        $fileData = $this->getOriginalJsonFileContentArray($filePath);

        $generatedFilePath = $this->getGeneratedFileSuffix($path);
        $generatedFileData = $this->getOriginalJsonFileContentArray($generatedFilePath);

        $translations = $this->getPreparedTranslations($fileData);
        $language = $this->getLanguage();
        $textVariables = $this->getTranslatedVariablesForLangData($language, $translations);

        $this->titleRecordContent = $titleRecordContent;
        $this->fileData = $fileData;
        $this->generatedFileData = $generatedFileData;
        $this->textVariables = $textVariables;

        return $this;
    }

    public function getFullContent(string $translatedName): string
    {
        $mainContent = $this->getOriginalHtmlFileContent('content-blocks/patron-content-block.html');

        $fileData = $this->fileData;
        $textVariables = $this->textVariables;

        $variables = [];
        $variables['date-of-birth'] = $this->getFormattedDates($fileData['born'] ?? self::UNKNOWN_SIGN);
        $variables['date-of-death'] = $this->getFormattedDates($fileData['died'] ?? self::UNKNOWN_SIGN);
        $variables['beatification'] = $this->getDateWithType($fileData['beatified'] ?? []);
        $variables['canonization'] = $this->getDateWithType($fileData['canonized'] ?? []);

        $dataLinksTableName = self::VARIABLE_NAME_SIGN . self::NAMES_INDEX . self::MODIFIER_SEPARATOR . self::MODIFIER_FIRST_ELEMENT . self::VARIABLE_NAME_SIGN;
        $variables['data-links-content-block'] = $this->getDataLinksContent($dataLinksTableName);

        $titleItemsContent = '';
        foreach ($fileData[self::TITLES_INDEX] ?? [] as $recordId => $recordData) {
            $titleItemsContent .= $this->getRecordContent($recordId);
        }
        $variables['title-items'] = $titleItemsContent;

        $mainContent = $this->getReplacedContent($mainContent, $variables);

        return $this->getReplacedContent($mainContent, $textVariables, true);
    }

    public function getRecordContent(string $recordId): string
    {
        $result = '';

        $titleRecordContent = $this->titleRecordContent;
        $titleRow = $this->fileData[self::TITLES_INDEX][$recordId] ?? [];

        $variables = [];
        $variables['record-id'] = $recordId;
        $variables['record-activeness-class'] = $this->getRecordActivenessClass($recordId);

        $dataLinksTableName = self::VARIABLE_NAME_SIGN . $this->getPreparedTranslationRecordKey(self::NAMES_INDEX, $recordId) . self::MODIFIER_SEPARATOR . self::MODIFIER_FIRST_ELEMENT . self::VARIABLE_NAME_SIGN;
        $variables['data-links-content-block'] = $this->getDataLinksContent($dataLinksTableName, self::PATRON_TITLES_PATH . $recordId);

        return $this->getReplacedContent($titleRecordContent, $variables);

        return $result;
    }

    private function getPreparedTranslations(array $data): array
    {
        $result = [];

        foreach ($data as $key => $values) {
            if (in_array($key, self::TRANSLATED_INDEXES)) {
                $result[$key] = $values;
            }
        }

        foreach ($data[self::TITLES_INDEX] ?? [] as $recordId => $recordData) {
            foreach ($recordData as $key => $values) {
                if (in_array($key, self::TITLES_TRANSLATED_INDEXES)) {
                    $preparedKey = $this->getPreparedTranslationRecordKey($key, $recordId);
                    $result[$preparedKey] = $values;
                }
            }
        }

        return $result;
    }

    private function getDataLinksContent(string $tableName, string $path = ''): string
    {
        return $this
            ->dataLinksContentBlock
            ->setData($this->fileData)
            ->prepare($path)
            ->getFullContent($tableName)
        ;
    }

    private function getDateWithType(array $data): string
    {
        $result = $data['date'] ?? null;
        $type = $data['type'] ?? null;

        if (is_null($result)) {
            $result = self::NON_EXISTENCE;
        } else {
            $result = $this->getFormattedDate($result);
            if (!is_null($type)) {
                $result .= " (#lang-$type-adverb#)";
            }
        }

        return $result;
    }

    private function getPreparedTranslationRecordKey(string $key, string $recordId): string
    {
        return 'record-' . $recordId . '-' . $key;
    }
}
