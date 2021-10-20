<?php

class FeastContentBlock extends ContentBlock implements ContentBlockInterface
{
    private const NAMES_INDEX = 'names';

    private const TRANSLATED_INDEXES = [
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
        $mainContent = $this->getOriginalHtmlFileContent('content-blocks/feast-content-block.html');

        $fileData = $this->fileData;
        $textVariables = $this->textVariables;

        $variables = [];

        $dataLinksTableName = self::VARIABLE_NAME_SIGN . self::NAMES_INDEX . self::MODIFIER_SEPARATOR . self::MODIFIER_FIRST_ELEMENT . self::VARIABLE_NAME_SIGN;
        $variables['data-links-content-block'] = $this->getDataLinksContent($dataLinksTableName);

        $mainContent = $this->getReplacedContent($mainContent, $variables);

        return $this->getReplacedContent($mainContent, $textVariables, true);
    }

    public function getRecordContent(string $recordId): string
    {
        return ''; //... no records possible
    }

    private function getPreparedTranslations(array $data): array
    {
        $result = [];

        foreach ($data as $key => $values) {
            if (in_array($key, self::TRANSLATED_INDEXES)) {
                $result[$key] = $values;
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
}
