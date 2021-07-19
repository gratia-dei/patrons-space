<?php

abstract class Content
{
    private const ENCODING = 'UTF-8';

    protected const VARIABLE_NAME_SIGN = '#';
    protected const MODIFIER_SEPARATOR = '|';

    protected const MODIFIER_CAPITALIZE = 'capitalize';
    protected const MODIFIER_CAPITALIZE_ALL = 'capitalize-all';
    protected const MODIFIER_ORIGINAL = 'original';
    protected const MODIFIER_UPPERCASE = 'uppercase';

    private const LANGUAGE_VARIABLE_NAME_BEFORE = 'lang-language-before-final-translation';
    private const LANGUAGE_VARIABLE_NAME_AFTER = 'lang-language';
    private const RESOURCE_PATH_SUFFIX_VARIABLE_NAME = 'resource-path-suffix';

    protected const DATA_ROOT_PARENT_DIRECTORY_PATH = '/data';

    private $environment;
    private $file;
    private $json;
    private $path;

    private $translatedLanguagesVariablesCache;

    public function __construct()
    {
        $this->environment = new Environment();
        $this->file = new File();
        $this->json = new Json();
        $this->path = new Path();
    }

    protected function getEnvironment(): Environment
    {
        return $this->environment;
    }

    protected function getLanguage(): string
    {
        return $this->environment->getHostSubdomainOnly();
    }

    protected function getResourcePathSuffixVariableName(): string
    {
        return self::RESOURCE_PATH_SUFFIX_VARIABLE_NAME;
    }

    protected function getOriginalJsonFileContentArray(string $jsonFileName): array
    {
        $jsonPath = $this->path->getDataPath($jsonFileName);
        $content = $this->file->getFileContent($jsonPath);
        $array = $this->json->decode($content);

        return $array;
    }

    protected function getOriginalHtmlFileContent(string $htmlFileName): string
    {
        $htmlPath = $this->path->getHtmlPath($htmlFileName);
        $content = $this->file->getFileContent($htmlPath);

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
        $result = $this->getTranslatedVariables($language, 'languages.json');

        $this->translatedLanguagesVariablesCache = $result;

        return $result;
    }

    protected function getTranslatedVariables(string $language, string $fileDataSubpath): array
    {
        $result = [];

        $variables = $this->getOriginalJsonFileContentArray($fileDataSubpath);
        foreach ($variables as $name => $values) {
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

    protected function getFullResourcePath(string $path): string
    {
        return rtrim($path, '/') . self::VARIABLE_NAME_SIGN . $this->getResourcePathSuffixVariableName() . self::VARIABLE_NAME_SIGN;
    }

    protected function getDataParentDirectoryPath(string $path): string
    {
        if ($path !== '/') {
            $path = dirname($path);
            if ($path === '/') {
                $path = self::DATA_ROOT_PARENT_DIRECTORY_PATH;
            }
        }

        return $path;
    }

    protected function getIndexFilePath(string $path): string
    {
        return $path . 'index.json';
    }

    private function getMissingTranslationMessage(string $originalLanguage): string
    {
        $originalMessage = self::VARIABLE_NAME_SIGN . self::LANGUAGE_VARIABLE_NAME_BEFORE . self::VARIABLE_NAME_SIGN
            . ': ' . self::VARIABLE_NAME_SIGN . $originalLanguage . self::VARIABLE_NAME_SIGN . ' (' . mb_strtoupper($originalLanguage) . ')';

        $languagesVariables = $this->getTranslatedLanguagesVariables();
        $replacedMessage = $this->getReplacedContent($originalMessage, $languagesVariables);

        return $replacedMessage;
    }

    private function getModifiedValue(string $value, array $modifiers): string
    {
        foreach ($modifiers as $modifier) {
            switch ($modifier) {
                case self::MODIFIER_UPPERCASE:
                    $value = mb_strtoupper($value);
                    break;
                case self::MODIFIER_CAPITALIZE:
                    $value = mb_strtoupper(mb_substr($value, 0, 1, self::ENCODING), self::ENCODING)
                        . mb_strtolower(mb_substr($value, 1, mb_strlen($value), self::ENCODING), self::ENCODING);
                    break;
                case self::MODIFIER_CAPITALIZE_ALL:
                    $value = mb_convert_case($value, MB_CASE_TITLE, self::ENCODING);
                    break;
            }
        }

        return $value;
    }
}
