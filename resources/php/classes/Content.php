<?php

abstract class Content extends Base
{
    protected const MAIN_FILE_DATA_INDEX = 'main-data';

    protected const ENCODING = 'UTF-8';

    protected const VARIABLE_NAME_SIGN = '#';
    protected const MODIFIER_SEPARATOR = '|';

    protected const MODIFIER_CAPITALIZE = 'capitalize';
    protected const MODIFIER_CAPITALIZE_ALL = 'capitalize-all';
    protected const MODIFIER_COMMA_SEPARATED_LIST = 'comma-separated-list';
    protected const MODIFIER_FIRST_ELEMENT = 'first-element';
    protected const MODIFIER_ORIGINAL = 'original';
    protected const MODIFIER_UPPERCASE = 'uppercase';
    protected const MODIFIER_WITHOUT_FIRST_ELEMENT = 'without-first-element';

    protected const DATA_ROOT_PARENT_DIRECTORY_PATH = '/data';

    private const CONTENT_ONLY_QUERY_PARAM_NAME = 'content-only';
    private const ACTIVE_RECORD_ID_QUERY_PARAM_NAME = 'active-record-id';

    private const LANGUAGE_VARIABLE_NAME_BEFORE = 'lang-language-before-final-translation';
    private const LANGUAGE_VARIABLE_NAME_AFTER = 'lang-language';
    private const UNKNOWN_LANGUAGE_SIGN = '';

    private $translatedLanguagesVariablesCache;

    protected function getLanguage(): string
    {
        return $this->getEnvironment()->getHostSubdomainOnly();
    }

    protected function getOriginalHtmlFileContent(string $htmlFileName): string
    {
        $htmlPath = $this->getPath()->getHtmlPath($htmlFileName);
        $content = $this->getFile()->getFileContent($htmlPath);

        return $content;
    }

    protected function getReplacedContent(string $content, array $variables, bool $showUsingOriginalLanguageInfo = false): string
    {
        if (preg_match_all('/' . self::VARIABLE_NAME_SIGN . '([-a-z0-9]+)([' . self::MODIFIER_SEPARATOR . '][-' . self::MODIFIER_SEPARATOR . 'a-z]+)?' . self::VARIABLE_NAME_SIGN . '/', $content, $matches)) {
            foreach (($matches[1] ?? []) as $key => $name) {
                $lookingPhrase = $matches[0][$key] ?? '';
                $modifiersString = $matches[2][$key] ?? '';
                $modifiers = explode(self::MODIFIER_SEPARATOR, trim($modifiersString, self::MODIFIER_SEPARATOR));
                $isOriginalModifier = in_array(self::MODIFIER_ORIGINAL, $modifiers);

                $value = $variables[$name] ?? null;
                if ($value === null || $isOriginalModifier) {
                    $nameWithOriginal = $name . self::MODIFIER_SEPARATOR . self::MODIFIER_ORIGINAL;
                    $valueArr = $variables[$nameWithOriginal] ?? null;

                    if ($valueArr !== null) {
                        $originalLanguage = array_key_first($valueArr);
                        $value = array_shift($valueArr);
                        $value = $this->getModifiedValue($value, $modifiers);

                        if ($value !== null && $showUsingOriginalLanguageInfo && !$isOriginalModifier) {
                            $message = $this->getMissingTranslationMessage($originalLanguage);
                            $value = '<span class="original-language-info" title="' . $message . '">' . $value . '</span>';
                        }
                    }
                } else {
                    $value = $this->getModifiedValue($value, $modifiers);
                }

                if ($value !== null) {
                    $content = str_replace($lookingPhrase, $value, $content);
                }
            }
        }

        return $content;
    }

    protected function getTranslatedLanguagesVariables(): array
    {
        $result = $this->translatedLanguagesVariablesCache;

        if (is_array($result)) {
            return $result;
        }

        $language = $this->getLanguage();
        $result = $this->getTranslatedVariables($language, 'languages' . self::DATA_FILE_EXTENSION);

        $this->translatedLanguagesVariablesCache = $result;

        return $result;
    }

    protected function getTranslatedVariables(string $language, string $fileDataSubpath): array
    {
        $result = [];

        $variables = $this->getOriginalJsonFileContentArray($fileDataSubpath);

        return $this->getTranslatedVariablesForLangData($language, $variables);
    }

    protected function getTranslatedVariablesForLangData(string $language, array $langData): array
    {
        $result = [];

        foreach ($langData as $name => $values) {
            $value = $values[$language] ?? null;
            if ($value !== null) {
                $result[$name] = $value;
            }

            $originalLanguage = array_key_first($values);
            $originalValue = reset($values);
            $nameWithLanguage = $name . self::MODIFIER_SEPARATOR . self::MODIFIER_ORIGINAL;
            $result[$nameWithLanguage][$originalLanguage] = $originalValue;
        }

        return $result;
    }

    protected function getFinallyTranslatedContent(string $content, array $websiteTranslatedVariables): string
    {
        $variables = [
            self::LANGUAGE_VARIABLE_NAME_BEFORE => self::VARIABLE_NAME_SIGN . self::LANGUAGE_VARIABLE_NAME_AFTER . self::VARIABLE_NAME_SIGN,
        ];
        $replacedContent = $this->getReplacedContent($content, $variables);
        $translatedContent = $this->getReplacedContent($replacedContent, $websiteTranslatedVariables);

        return $translatedContent;
    }

    protected function stripTags(string $content): string
    {
        return strip_tags($content);
    }

    protected function getActiveRecordId(): ?string
    {
        $queryParams = $this->getEnvironment()->getRequestQueryParams();

        return $queryParams[self::ACTIVE_RECORD_ID_QUERY_PARAM_NAME] ?? null;
    }

    protected function getLinkWithActiveRecordIdForAnchor(string $link): string
    {
        return preg_replace('/^([^#]+)[#](.*)$/U', '\1?' . self::ACTIVE_RECORD_ID_QUERY_PARAM_NAME . '=\2#\2', $link);
    }

    protected function isContentOnlyMode(): bool
    {
        $requestParams = $this->getEnvironment()->getRequestQueryParams();

        return !empty($requestParams[self::CONTENT_ONLY_QUERY_PARAM_NAME] ?? '');
    }

    private function getMissingTranslationMessage(string $originalLanguage): string
    {
        $originalMessage = self::VARIABLE_NAME_SIGN . self::LANGUAGE_VARIABLE_NAME_BEFORE . self::VARIABLE_NAME_SIGN
            . ': ' . self::VARIABLE_NAME_SIGN . $originalLanguage . self::VARIABLE_NAME_SIGN . ' (' . mb_strtoupper($originalLanguage) . ')';

        $languagesVariables = $this->getTranslatedLanguagesVariables();
        $replacedMessage = $this->getReplacedContent($originalMessage, $languagesVariables);

        $unknownLanguagesReplacedMessage = preg_replace('/#[a-z]+#/', self::UNKNOWN_LANGUAGE_SIGN, $replacedMessage);

        return $unknownLanguagesReplacedMessage;
    }

    private function getModifiedValue($value, array $modifiers): string
    {
        foreach ($modifiers as $modifier) {
            switch ($modifier) {
                //--- string to string modifiers:
                case self::MODIFIER_CAPITALIZE:
                    $value = mb_strtoupper(mb_substr($value, 0, 1, self::ENCODING), self::ENCODING)
                        . mb_substr($value, 1, mb_strlen($value), self::ENCODING);
                    break;
                case self::MODIFIER_CAPITALIZE_ALL:
                    $value = mb_convert_case($value, MB_CASE_TITLE, self::ENCODING);
                    break;
                case self::MODIFIER_UPPERCASE:
                    $value = mb_strtoupper($value);
                    break;

                //--- array to string modifiers:
                case self::MODIFIER_COMMA_SEPARATED_LIST:
                    if (is_array($value)) {
                        $value = implode(', ', $value);
                    }
                    break;
                case self::MODIFIER_FIRST_ELEMENT:
                    $firstKey = array_key_first($value);
                    $value = $value[$firstKey] ?? null;
                    break;

                //--- array to array modifiers:
                case self::MODIFIER_WITHOUT_FIRST_ELEMENT:
                    array_shift($value);
                    break;
            }
        }

        return $value;
    }

    protected function getConsolidatedDataFilesArray(string $path): array
    {
        $result = [];

        $staticFilePath = $this->getDataFileSuffix($path);
        $staticFileData = $this->getOriginalJsonFileContentArray($staticFilePath);
        $result[self::MAIN_FILE_DATA_INDEX] = $this->getOriginalJsonFileContentArray($staticFilePath);

        $generatedFilePath = $this->getGeneratedFileSuffix($path);
        $generatedFileData = $this->getOriginalJsonFileContentArray($generatedFilePath);
        foreach ($generatedFileData as $index => $data) {
            $result[$index] = $data;
        }

        return $result;
    }
}
