<?php

class BreadcrumbsContentBlock extends ContentBlock implements ContentBlockInterface
{
    private const GLOBAL_VARIABLES_KEY_NAME = 'breadcrumbs-path-names';

    private const PATH_SEPARATOR = '###';
    private const RECORD_SEPARATOR = ' &gt; ';
    private const ACTIVE_LINK_NAME_PREFIX = '###';

    private const MAIN_PAGE_VARIABLE = self::VARIABLE_NAME_SIGN . 'lang-main-page' . self::MODIFIER_SEPARATOR . self::MODIFIER_CAPITALIZE . self::VARIABLE_NAME_SIGN;
    private const DATA_VARIABLE = self::VARIABLE_NAME_SIGN . 'lang-data' . self::MODIFIER_SEPARATOR . self::MODIFIER_CAPITALIZE . self::VARIABLE_NAME_SIGN;

    private const BREADCRUMBS_HIDE_DATA_ELEMENT_PATHS = [
        'cards' => true,
        'dates' => false,
        'files' => false,
    ];

    private GlobalVariables $globalVariables;

    private bool $showDataElement = false;
    private string $activeLinkContent;
    private string $inactiveLinkContent;
    private bool $showMainPage = false;
    private array $pathElements = [];

    public function __construct()
    {
        parent::__construct();

        $this->globalVariables = GlobalVariables::getInstance();
    }

    public function getHideDataElementPaths(): array
    {
        return self::BREADCRUMBS_HIDE_DATA_ELEMENT_PATHS;
    }

    public function getPathWithContext(string $fullPath, string $contextPath = ''): string
    {
        return $fullPath . self::PATH_SEPARATOR . $contextPath;
    }

    public function showDataElement(bool $showDataElement): ContentBlock
    {
        $this->showDataElement = $showDataElement;

        return $this;
    }

    public function prepare(string $originalPath): ContentBlock
    {
        $activeLinkContent = $this->getOriginalHtmlFileContent('items/breadcrumbs-active-link.html');
        $inactiveLinkContent = $this->getOriginalHtmlFileContent('items/breadcrumbs-inactive-link.html');

        $showMainPage = true;

        $tmpPaths = explode(self::PATH_SEPARATOR, $originalPath);
        $tmpFullPath = $this->getTidyPath($tmpPaths[0]);
        $tmpContextPath = $tmpPaths[1] ?? '';
        if ($tmpContextPath === '/') {
            $showMainPage = false;
        }
        $tmpContextPath = $this->getTidyPath($tmpContextPath);
        if ($tmpContextPath !== '') {
            $showMainPage = false;
        }

        $path = $tmpFullPath;
        $contextPath = '';
        if (strpos($tmpFullPath, $tmpContextPath) === 0) {
            $tmpPath = mb_substr($tmpFullPath, mb_strlen($tmpContextPath));
            if ($tmpFullPath === $tmpContextPath || mb_substr($tmpPath, 0, 1) === '/') {
                $path = $this->getTidyPath($tmpPath);
                $contextPath = $tmpContextPath;
            }
        }

        $this->activeLinkContent = $activeLinkContent;
        $this->inactiveLinkContent = $inactiveLinkContent;
        $this->showMainPage = $showMainPage;
        $this->pathElements = $this->getPathElements($showMainPage, $path, $contextPath);

        return $this;
    }

    public function getFullContent(string $translatedName): string
    {
        $showMainPage = $this->showMainPage;
        $pathElements = $this->pathElements;
        if (!$showMainPage && count($pathElements) === 1) {
            return '';
        }

        $result = ltrim("$translatedName: ", ': ');

        $useSeparator = false;
        $recordId = 0;
        foreach ($pathElements as $path => $name) {
            if ($useSeparator) {
                $result .= $this->getRecordSeparator();
            }

            if ($showMainPage || $recordId > 0) {
                $result .= $this->getRecordContent($recordId);
                $useSeparator = true;
            }

            $recordId++;
        }

        return $result;
    }

    public function getRecordContent(string $recordId): string
    {
        $activeLinkContent = $this->activeLinkContent;
        $inactiveLinkContent = $this->inactiveLinkContent;
        $pathElements = $this->pathElements;

        $keys = array_keys($pathElements);
        $link = $keys[$recordId];
        $name = $pathElements[$link];

        $linkContent = $inactiveLinkContent;
        if (mb_strpos($name, self::ACTIVE_LINK_NAME_PREFIX) === 0) {
            $name = mb_substr($name, mb_strlen(self::ACTIVE_LINK_NAME_PREFIX));
            $linkContent = $activeLinkContent;
        }
        $variables = [
            'name' => $name,
            'link' => $this->getRecordIdPathWithNameExtension($link, $name),
        ];

        return $this->getReplacedContent($linkContent, $variables);
    }

    public function getRecordSeparator(): string
    {
        return self::RECORD_SEPARATOR;
    }

    public function getLinkWithAnchor(string $path, string $anchor): string
    {
        $activeLinkContent = $this->activeLinkContent;

        $variables = [
            'link' => $this->getLinkWithActiveRecordIdForAnchor("$path#$anchor"),
            'name' => "#$anchor",
        ];

        return $this->getReplacedContent($activeLinkContent, $variables);
    }

    private function getTidyPath(string $path): string
    {
        return $this->getEnvironment()->getTidyPath($path);
    }

    private function getPathElements(bool $showMainPage, string $path, string $contextPath): array
    {
        $result = [];

        $showDataElement = $this->showDataElement;

        $result['/'] = ($showMainPage ? self::ACTIVE_LINK_NAME_PREFIX : '') . self::MAIN_PAGE_VARIABLE;
        if ($showMainPage && $contextPath === '' && $showDataElement) {
            $result[self::DATA_ROOT_PARENT_DIRECTORY_PATH] = self::ACTIVE_LINK_NAME_PREFIX . self::DATA_VARIABLE;
        }
        if ($path !== '') {
            $pathElements = explode('/', $path);

            $currentPath = $contextPath;
            foreach ($pathElements as $pathElement) {
                $currentPath = '/' . ltrim("$currentPath/$pathElement", '/');

                $result[$currentPath] = $this->getPathElementName($currentPath, $pathElement);
            }
        }

        return $result;
    }

    private function getPathElementName(string $path, string $element): string
    {
        $result = [];

        $found = false;
        $globalVariables = $this->globalVariables;
        $names = $globalVariables->get(self::GLOBAL_VARIABLES_KEY_NAME);

        if (isset($names[$path])) {
            list($name, $found) = $names[$path];
        } else {
            if ($this->dataPathExists($path)
                || $this->dataPathExists($this->getDataFileSuffix($path))
                || $this->dataPathExists($this->getGeneratedFileSuffix($path))
            ) {
                $found = true;
            }

            $language = $this->getLanguage();
            $indexFilePath = $this->getIndexFilePath(dirname($path));
            $indexVariables = $this->getTranslatedVariables($language, $indexFilePath);
            if (empty($indexVariables)) {
                $indexFilePath = $this->getIndexFilePath(dirname($path), true);
                $indexVariables = $this->getTranslatedVariables($language, $indexFilePath);
            }

            $name = $this->getReplacedContent(self::VARIABLE_NAME_SIGN . $element . self::VARIABLE_NAME_SIGN, $indexVariables, true);
            $name = preg_replace('/^' . self::VARIABLE_NAME_SIGN . '(.+)' . self::VARIABLE_NAME_SIGN . '$/', '\1', $name);

            if (!$found && $element === $name) {
                $isActive = self::BREADCRUMBS_HIDE_DATA_ELEMENT_PATHS[$element] ?? null;
                if (!is_null($isActive)) {
                    $name = self::VARIABLE_NAME_SIGN . self::LANG_VARIABLE_PREFIX . $element . self::MODIFIER_SEPARATOR . self::MODIFIER_CAPITALIZE . self::VARIABLE_NAME_SIGN;
                    $found = $isActive;
                }
            }

            $names[$path] = [$name, $found];
            $globalVariables->set(self::GLOBAL_VARIABLES_KEY_NAME, $names);
        }

        return ($found ? self::ACTIVE_LINK_NAME_PREFIX : '') . $name;
    }
}
