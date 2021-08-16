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
        $variables = $this->getTranslatedVariablesForLangData($language, $translations);

        $variables['date-of-birth'] = $this->getFormattedDates($fileData['born'] ?? self::UNKNOWN_SIGN);
        $variables['date-of-death'] = $this->getFormattedDates($fileData['died'] ?? self::UNKNOWN_SIGN);
        $variables['beatification'] = $this->getDateWithType($fileData['beatified'] ?? []);
        $variables['canonization'] = $this->getDateWithType($fileData['canonized'] ?? []);

        return $this->getReplacedContent($content, $variables, true);
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
