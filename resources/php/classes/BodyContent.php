<?php

class BodyContent extends Content
{
    private const RESOURCE_PATH_SUFFIX_FOR_MAIN_CONTENT_ONLY = 'content';

    private const ORIGINAL_VARIABLE_NAME = 'lang-original';

    private const NEWLINE = "\n";
    private const ORIGINAL_LANGUAGE_CODE = '??';
    private const ACTIVE_LANGUAGE_CLASS = 'active';

    private $mainContentManager;

    public function __construct()
    {
        parent::__construct();
        $this->mainContentManager = new MainContentManager();
    }

    public function getTitleAndContent(): array
    {
        $protocol = $this->getEnvironment()->getHostProtocol();
        $domain = $this->getEnvironment()->getHostDomain();
        $requestPath = $this->getEnvironment()->getRequestPath();
        $httpStatusCode = $this->getEnvironment()->getHttpStatusCode();

        $variables = [];
        if ($this->isContentOnlySuffixOnRequestPath($requestPath)) {
            $htmlFileName = 'body-content.html';
        } else {
            $htmlFileName = 'body-full.html';
            $variables['selected-language'] = $this->getSelectedLanguageName();
            $variables['selectable-languages-list'] = $this->getSelectableLanguagesList($protocol, $domain, $requestPath);
        }
        list($title, $variables['content']) = $this->mainContentManager->getTitleAndContent($requestPath, $httpStatusCode);

        $originalContent = $this->getOriginalHtmlFileContent($htmlFileName);
        $replacedContent = $this->getReplacedContent($originalContent, $variables);

        return [$title, $replacedContent];
    }

    private function isContentOnlySuffixOnRequestPath(string &$requestPath): bool
    {
        $result = false;

        if (basename($requestPath) === self::RESOURCE_PATH_SUFFIX_FOR_MAIN_CONTENT_ONLY) {
            $requestPath = dirname($requestPath);
            $result = true;
        }

        return $result;
    }

    private function getSelectedLanguageName(): string
    {
        $selectedLanguage = $this->getEnvironment()->getHostSubdomainOnly();
        $languages = $this->getTranslatedLanguagesVariables();

        $languageName = $languages[$selectedLanguage] ?? null;
        if ($languageName !== null) {
            return $languageName;
        }

        $originalLanguageNameArray = $languages[$selectedLanguage . self::MODIFIER_SEPARATOR . self::MODIFIER_ORIGINAL] ?? null;
        if ($originalLanguageNameArray !== null) {
            return reset($originalLanguageNameArray);
        }

        return self::VARIABLE_NAME_SIGN . self::ORIGINAL_VARIABLE_NAME . self::VARIABLE_NAME_SIGN;
    }

    private function getSelectableLanguagesList(string $protocol, string $domain, string $requestPath): string
    {
        $content = '';

        $selectedLanguage = $this->getEnvironment()->getHostSubdomainOnly();
        $optionContent = $this->getOriginalHtmlFileContent('selectable-languages-list-item.html');
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

    private function getSelectableLanguagesListValues(): array
    {
        $codes = ['' => ''];
        $translated = ['' => self::VARIABLE_NAME_SIGN . self::ORIGINAL_VARIABLE_NAME . self::VARIABLE_NAME_SIGN];
        $original = ['' => self::VARIABLE_NAME_SIGN . self::ORIGINAL_VARIABLE_NAME . self::MODIFIER_SEPARATOR . self::MODIFIER_ORIGINAL . self::VARIABLE_NAME_SIGN];

        $allLanguages = $this->getTranslatedLanguagesVariables();
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
}
