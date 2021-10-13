<?php

class BodyContent extends Content
{
    private const ORIGINAL_VARIABLE_NAME = 'lang-original';

    private const NEWLINE = "\n";
    private const ORIGINAL_LANGUAGE_CODE = '??';
    private const ACTIVE_LANGUAGE_CLASS = 'active';

    private const PATH_TITLE_VARIABLE = self::VARIABLE_NAME_SIGN . 'lang-path' . self::MODIFIER_SEPARATOR . self::MODIFIER_CAPITALIZE . self::VARIABLE_NAME_SIGN;
    private const BREADCRUMBS_HIDE_DATA_ELEMENT_PATHS = [
        'dates',
    ];

    private $mainContentRouter;
    private $breadcrumbsContentBlock;

    public function __construct()
    {
        parent::__construct();
        $this->mainContentRouter = new MainContentRouter();
        $this->breadcrumbsContentBlock = new BreadcrumbsContentBlock();
    }

    public function getTitleAndContent(): array
    {
        $protocol = $this->getEnvironment()->getHostProtocol();
        $domain = $this->getEnvironment()->getHostMainDomainOnly();
        $requestPath = $this->getEnvironment()->getRequestPath();
        $httpStatusCode = $this->getEnvironment()->getHttpStatusCode();

        $variables = [];
        if ($this->isContentOnlyMode()) {
            $htmlFileName = 'body-content.html';
        } else {
            $htmlFileName = 'body-full.html';
            $variables['selected-language'] = $this->getSelectedLanguageName();
            $variables['selectable-languages-list'] = $this->getSelectableLanguagesList($protocol, $domain, $requestPath);
            $variables['breadcrumbs-content-block'] = $this->getBreadcrumbsContent($requestPath);
        }

        list($title, $variables['content']) = $this->mainContentRouter->getTitleAndContent($requestPath, $httpStatusCode);

        $strippedTitle = $this->stripTags($title);

        $originalContent = $this->getOriginalHtmlFileContent($htmlFileName);
        $replacedContent = $this->getReplacedContent($originalContent, $variables);

        return [$strippedTitle, $replacedContent];
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
        $queryParamsString = $this->getEnvironment()->getRequestQueryParamsString();

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
                'href' => $protocol . ltrim($code . '.' . $domain . $requestPath . $queryParamsString, '.'),
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

    private function getBreadcrumbsContent(string $requestPath): string
    {
        $showDataElement = true;
        foreach (self::BREADCRUMBS_HIDE_DATA_ELEMENT_PATHS as $path) {
            if (mb_strpos("$requestPath/", "/$path/") === 0) {
                $showDataElement = false;
            }
        }

        $bcFullPath = $requestPath === self::DATA_ROOT_PARENT_DIRECTORY_PATH ? '/' : $requestPath;
        $bcContextPath = $requestPath === '/' ? '/' : '';
        $bcPath = $this->breadcrumbsContentBlock->getPathWithContext($bcFullPath, $bcContextPath);

        return $this->breadcrumbsContentBlock
            ->showDataElement($showDataElement)
            ->prepare($bcPath)
            ->getFullContent(self::PATH_TITLE_VARIABLE)
        ;
    }
}
