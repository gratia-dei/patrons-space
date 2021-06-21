<?php

namespace {

    class Content
    {
        const VARIABLE_NAME_SIGN = '#';
        const MODIFIER_SEPARATOR = '|';

        const MODIFIER_ORIGINAL = 'original';
        const MODIFIER_UPPERCASE = 'uppercase';

        const TITLE_VARIABLE = self::VARIABLE_NAME_SIGN . 'service-name' . self::MODIFIER_SEPARATOR . self::MODIFIER_ORIGINAL . self::VARIABLE_NAME_SIGN;
        const ORIGINAL_LANGUAGE_VARIABLE = self::VARIABLE_NAME_SIGN . 'original-language' . self::VARIABLE_NAME_SIGN;

        private $language;
        private $websiteTranslatedVariables;

        public function __construct(string $language, string $languageVariablesFilePath, string $languagesFilePath)
        {
            $this->language = $language;
            $this->websiteTranslatedVariables = $this->getTranslatedVariables($language, $languageVariablesFilePath);
            $this->languages = $this->getTranslatedVariables($language, $languagesFilePath);
        }

        public function getWebsiteTranslatedVariables(): array
        {
            return $this->websiteTranslatedVariables;
        }

        public function getLanguages(): array
        {
            return $this->languages;
        }

        public function getFileContent(string $filePath, array $variables = []): string
        {
            $content = file_get_contents($filePath);

            return $this->getReplacedContent($content, $variables);
        }

        public function getReplacedContent(string $content, array $variables, bool $showUsingDefaultLanguageInfo = false): string
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
                            if ($value !== null && $showUsingDefaultLanguageInfo && !$isOriginalModifier) {
                                $message = $this->getMissingTranslationMessage($originalLanguage);
                                $value = '<span title="' . $message . '">' . $value . '</span>';
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

        public function getMissingTranslationMessage(string $originalLanguage): string
        {
            $message = self::ORIGINAL_LANGUAGE_VARIABLE . ': ' . self::VARIABLE_NAME_SIGN . $originalLanguage . self::VARIABLE_NAME_SIGN . ' (' . $originalLanguage . ')';

            $languagesVariables = $this->getLanguages();
            $message = $this->getReplacedContent($message, $languagesVariables);

            $websiteTranslatedVariables = $this->getWebsiteTranslatedVariables();
            $message = $this->getReplacedContent($message, $websiteTranslatedVariables);

            return $message;
        }

        public function getModifiedValue(string $value, array $modifiers): string
        {
            foreach ($modifiers as $modifier) {
                switch ($modifier) {
                    case self::MODIFIER_UPPERCASE:
                        $value = mb_strtoupper($value);
                        break;
                }
            }

            return $value;
        }

        public function getJsonFileArray(string $filePath): array
        {
            $content = $this->getFileContent($filePath);

            return json_decode($content, true);
        }

        public function getTranslatedVariables(string $language, string $filePath): array
        {
            $result = [];

            $variables = $this->getJsonFileArray($filePath);
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

        public function getTitle(): string
        {
            $variables = $this->getWebsiteTranslatedVariables();

            return $this->getReplacedContent(self::TITLE_VARIABLE, $variables);
        }

        public function getContent(): string
        {
            $variables = $this->getWebsiteTranslatedVariables();
            $content = '#comming-soon# ...';

            return $this->getReplacedContent($content, $variables, true);
        }
    }
}
