<?php

class PopesContentBlock extends ContentBlock implements ContentBlockInterface
{
    private const NAME_INDEX = 'name';
    private const PONTIFICATE_INDEX = 'pontificate';

    private const TRANSLATED_INDEXES = [
        self::NAME_INDEX,
    ];

    private $popeItemContent;
    private $fileData;
    private $generatedFileData;
    private $textVariables;

    public function prepare(string $path): ContentBlock
    {
        $popeItemContent = $this->getOriginalHtmlFileContent('items/pope-item.html');

        $filePath = $this->getDataFileSuffix($path);
        $fileData = $this->getOriginalJsonFileContentArray($filePath);

        $generatedFilePath = $this->getGeneratedFileSuffix($path);
        $generatedFileData = $this->getOriginalJsonFileContentArray($generatedFilePath);

        $translations = $this->getPreparedTranslations($fileData, $generatedFileData[self::DATA_LINKS_GENERATED_FILES_INDEX] ?? []);
        $language = $this->getLanguage();
        $textVariables = $this->getTranslatedVariablesForLangData($language, $translations);

        $this->popeItemContent = $popeItemContent;
        $this->fileData = $fileData;
        $this->generatedFileData = $generatedFileData;
        $this->textVariables = $textVariables;

        return $this;
    }

    public function getFullContent(string $translatedName): string
    {
        $mainContent = $this->getOriginalHtmlFileContent('content-blocks/popes-content-block.html');

        $fileData = $this->fileData;

        $variables = [];
        $variables['popes-title'] = $translatedName;

        $popeItemsContent = '';
        foreach ($fileData ?? [] as $recordId => $recordData) {
            $popeItemsContent .= $this->getRecordContent($recordId);
        }
        $variables['popes-items'] = $popeItemsContent;

        return $this->getReplacedContent($mainContent, $variables);
    }

    public function getRecordContent(string $recordId): string
    {
        $popeItemContent = $this->popeItemContent;
        $popeRow = $this->fileData[$recordId] ?? [];
        $textVariables = $this->textVariables;

        $name = self::VARIABLE_NAME_SIGN . $recordId . '-' . self::NAME_INDEX . self::VARIABLE_NAME_SIGN;
        $description = implode(' - ', $popeRow[self::PONTIFICATE_INDEX] ?? []);

        $variables = [];
        $variables['record-id'] = $recordId;
        $variables['record-activeness-class'] = $this->getRecordActivenessClass($recordId);
        $variables['pope-name'] = $name;
        $variables['pope-description'] = $description;

        $content = $this->getReplacedContent($popeItemContent, $variables);

        return $this->getReplacedContent($content, $textVariables, true);
    }

    private function getPreparedTranslations(array $data, array $aliases): array
    {
        $result = [];

        foreach ($data as $key => $popeData) {
            foreach ($popeData as $field => $values) {
                if (in_array($field, self::TRANSLATED_INDEXES)) {
                    foreach ($values as $language => $text) {
                        $result["$key-$field"][$language] = $this->getTextWithSpecialLinks($text, $aliases[$key] ?? []);
                    }
                }
            }
        }

        return $result;
    }
}
