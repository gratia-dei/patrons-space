<?php

class BodyContent extends Content
{
    private const RESOURCE_PATH_SUFFIX_FOR_MAIN_CONTENT_ONLY = 'content';

    private const ORIGINAL_VARIABLE_NAME = 'lang-original';

    private const NEWLINE = "\n";
    private const ORIGINAL_LANGUAGE_CODE = '??';
    private const ACTIVE_LANGUAGE_CLASS = 'active';

    private $mainContentRouter;

    public function __construct()
    {
        parent::__construct();
        $this->mainContentRouter = new MainContentRouter();
    }

    public function getTitleAndContent(): array
    {
        $protocol = $this->getEnvironment()->getHostProtocol();
        $domain = $this->getEnvironment()->getHostMainDomainOnly();
        $requestPath = $this->getEnvironment()->getRequestPath();
        $httpStatusCode = $this->getEnvironment()->getHttpStatusCode();

        $requestPathBasename = basename($requestPath);

        $variables = [];
        $resourcePathSuffix = '/';
        if ($requestPathBasename === self::RESOURCE_PATH_SUFFIX_FOR_MAIN_CONTENT_ONLY) {
            $htmlFileName = 'body-content.html';
            $requestPath = dirname($requestPath);
            $resourcePathSuffix = '/' . $requestPathBasename;
        } else {
            $htmlFileName = 'body-full.html';
            $variables['selected-language'] = $this->getSelectedLanguageName();
            $variables['selectable-languages-list'] = $this->getSelectableLanguagesList($protocol, $domain, $requestPath);
        }
        list($title, $variables['content']) = $this->mainContentRouter->getTitleAndContent($requestPath, $httpStatusCode);

        $strippedTitle = $this->stripTags($title);

        $originalContent = $this->getOriginalHtmlFileContent($htmlFileName);
        $replacedContent = $this->getReplacedContent($originalContent, $variables);

        $resourcePathSuffixVariableName = $this->getResourcePathSuffixVariableName();
        $variables = [
            $resourcePathSuffixVariableName => $resourcePathSuffix,
        ];
        $replacedAgainContent = $this->getReplacedContent($replacedContent, $variables);

        return [$strippedTitle, $replacedAgainContent];
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
        $optionContent = $this->getOriginalHtmlFileContent('items/selectable-languages-list-item.html');
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
