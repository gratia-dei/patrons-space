<?php

class PatronContentBlock extends ContentBlock implements ContentBlockInterface
{
    private const NAMES_INDEX = 'names';

    private const TRANSLATED_INDEXES = [
        self::NAMES_INDEX
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
        $filePath = $this->getDataFileSuffix($path);
        $fileData = $this->getOriginalJsonFileContentArray($filePath);

        $generatedFilePath = $this->getGeneratedFileSuffix($path);
        $generatedFileData = $this->getOriginalJsonFileContentArray($generatedFilePath);

        $translations = $this->getPreparedTranslations($fileData);
        $language = $this->getLanguage();
        $textVariables = $this->getTranslatedVariablesForLangData($language, $translations);

        $this->fileData = $fileData;
        $this->generatedFileData = $generatedFileData;
        $this->textVariables = $textVariables;

        return $this;
    }

    public function getFullContent(string $translatedName): string
    {
        $content = $this->getOriginalHtmlFileContent('content-blocks/patron-content-block.html');

        $fileData = $this->fileData;
        $textVariables = $this->textVariables;

        $variables = [];
        $variables['date-of-birth'] = $this->getFormattedDates($fileData['born'] ?? self::UNKNOWN_SIGN);
        $variables['date-of-death'] = $this->getFormattedDates($fileData['died'] ?? self::UNKNOWN_SIGN);
        $variables['beatification'] = $this->getDateWithType($fileData['beatified'] ?? []);
        $variables['canonization'] = $this->getDateWithType($fileData['canonized'] ?? []);

        $dataLinksTableName = self::VARIABLE_NAME_SIGN . self::NAMES_INDEX . self::MODIFIER_SEPARATOR . self::MODIFIER_FIRST_ELEMENT . self::VARIABLE_NAME_SIGN;
        $variables['data-links-content-block'] = $this->getDataLinksContent($dataLinksTableName);

        $content = $this->getReplacedContent($content, $variables);

        return $this->getReplacedContent($content, $textVariables, true);
    }

    public function getRecordContent(string $recordId): string
    {
        return ''; //... to do for each patron's title sections
    }

    private function getPreparedTranslations(array $data): array
    {
        $result = [];

        foreach ($data as $key => $values) {
            if (in_array($key, self::TRANSLATED_INDEXES)) {
                $result[$key] = $values;
            }
        }

        return $this->getTranslationsWithAllConvertedFieldsAdded($result);
    }

    private function getTranslationsWithAllConvertedFieldsAdded(array $data): array
    {
        return $data;
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
        } else if (!is_null($type)) {
            $result .= " (#lang-$type-adverb#)";
        }

        return $result;
    }
}
