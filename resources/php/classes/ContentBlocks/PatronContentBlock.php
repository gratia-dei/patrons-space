<?php

class PatronContentBlock extends ContentBlock implements ContentBlockInterface
{
    private const NAMES_INDEX = 'names';

    private const TRANSLATED_INDEXES = [
        self::NAMES_INDEX
    ];

    public function getContent(string $directoryPath, string $fileName, array $fileData, string $fileNameTranslated): string
    {
        $content = $this->getOriginalHtmlFileContent('content-blocks/patron-content-block.html');

        $translations = $this->getPreparedTranslations($fileData);
        $language = $this->getLanguage();
        $textVariables = $this->getTranslatedVariablesForLangData($language, $translations);

        //...

        return $this->getReplacedContent($content, $textVariables, true);
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
}
