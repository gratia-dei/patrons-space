<?php

namespace {

    class Content
    {
        const ENCODING = 'UTF-8';

        const VARIABLE_NAME_SIGN = '#';
        const MODIFIER_SEPARATOR = '|';
        const NEWLINE = "\n";

        const MODIFIER_CAPITALIZE = 'capitalize';
        const MODIFIER_CAPITALIZE_ALL = 'capitalize-all';
        const MODIFIER_ORIGINAL = 'original';
        const MODIFIER_UPPERCASE = 'uppercase';

        const LANGUAGE_VARIABLE_NAME = 'lang-language';
        const ORIGINAL_VARIABLE_NAME = 'lang-original';

        const ORIGINAL_LANGUAGE_CODE = '??';
        const ACTIVE_LANGUAGE_CLASS = 'active';
        const TITLE_VARIABLE = self::VARIABLE_NAME_SIGN . 'lang-service-name' . self::MODIFIER_SEPARATOR . self::MODIFIER_ORIGINAL . self::VARIABLE_NAME_SIGN;

        private $language;
        private $websiteTranslatedVariables;

        public function __construct(string $language, string $languageVariablesFilePath, string $languagesFilePath)
        {
            $this->language = $language;
            $this->websiteTranslatedVariables = $this->getTranslatedVariables($language, $languageVariablesFilePath);
            $this->languages = $this->getTranslatedVariables($language, $languagesFilePath);
        }

        public function getLanguage(): string
        {
            return $this->language;
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
            $message = self::VARIABLE_NAME_SIGN . self::LANGUAGE_VARIABLE_NAME . self::VARIABLE_NAME_SIGN
                . ': ' . self::VARIABLE_NAME_SIGN . $originalLanguage . self::VARIABLE_NAME_SIGN . ' (' . $originalLanguage . ')';

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
            return self::TITLE_VARIABLE;
        }

        public function getContent(): string
        {
            $content = '#lang-comming-soon# ...';

            return $content;
        }

        public function getSelectedLanguage(): string
        {
            $selectedLanguage = $this->getLanguage();
            $languages = $this->getLanguages();

            $languageName = $languages[$selectedLanguage] ?? null;
            if ($languageName !== null) {
                return $languageName;
            }

            $languageArr = $languages[$selectedLanguage . self::MODIFIER_SEPARATOR . self::MODIFIER_ORIGINAL] ?? null;
            if ($languageArr !== null) {
                return reset($languageArr);
            }

            $variables = $this->getWebsiteTranslatedVariables();
            $content = self::VARIABLE_NAME_SIGN . self::ORIGINAL_VARIABLE_NAME . self::VARIABLE_NAME_SIGN;

            return $this->getReplacedContent($content, $variables, true);
        }

        public function getSelectableLanguagesListValues(): array
        {
            $codes = ['' => ''];
            $translated = ['' => self::VARIABLE_NAME_SIGN . self::ORIGINAL_VARIABLE_NAME . self::VARIABLE_NAME_SIGN];
            $original = ['' => self::VARIABLE_NAME_SIGN . self::ORIGINAL_VARIABLE_NAME . self::MODIFIER_SEPARATOR . self::MODIFIER_ORIGINAL . self::VARIABLE_NAME_SIGN];

            $allLanguages = $this->getLanguages();
            foreach ($allLanguages as $codeOriginal => $name) {
                $code = preg_replace('/[' . self::MODIFIER_SEPARATOR . '].*$/', '', $codeOriginal);
                $codes[$code] = $code;
                if (is_array($name)) {
                    $original[$code] = reset($name);
                } else {
                    $translated[$code] = $name;
                }
            }

            return [$codes, $translated, $original];
        }

        public function getSelectableLanguagesList(
            string $protocol,
            string $domain,
            string $requestPath,
            string $selectableLanguagesListHtmlFilePath
        ): string {
            $content = '';

            $selectedLanguage = $this->getLanguage();
            $optionContent = $this->getFileContent($selectableLanguagesListHtmlFilePath);
            list($codesList, $translatedNamesList, $originalNamesList) = $this->getSelectableLanguagesListValues();

            foreach ($codesList as $code) {
                if ($code === '') {
                    $name = '<b>' . mb_strtoupper(self::ORIGINAL_LANGUAGE_CODE) . '</b>: ' . $translatedNamesList[$code];
                } else {
                    $name = '<b>' . mb_strtoupper($code) . '</b>: ' . $originalNamesList[$code];

                    if (isset($translatedNamesList[$code])
                        && isset($originalNamesList[$code])
                        && str_replace(self::MODIFIER_SEPARATOR . self::MODIFIER_ORIGINAL, '', $originalNamesList[$code]) !== $translatedNamesList[$code]
                    ) {
                        $name .= ' (' . $translatedNamesList[$code] . ')';
                    }
                }
                $variables = [
                    'href' => $protocol . ltrim($code . '.' . $domain . $requestPath, '.'),
                    'name' => $name,
                    'class' => $selectedLanguage === $code ? ' ' . self::ACTIVE_LANGUAGE_CLASS : '',
                ];
                $content .= $this->getReplacedContent($optionContent, $variables, true) . self::NEWLINE;
            }

            return $content;
        }
    }
}
