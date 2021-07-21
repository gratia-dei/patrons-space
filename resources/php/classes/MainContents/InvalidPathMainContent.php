<?php

class InvalidPathMainContent extends MainContent implements MainContentInterface
{
    private const INVALID_LOCATION_VARIABLE = 'lang-invalid-location';

    private $directoryPath;

    public function configure(string $path): bool
    {
        $this->directoryPath = $this->getDataParentDirectoryPath($path);

        return true;
    }

    public function getTitle(string $prefix): string
    {
        return $prefix . ': ' . self::VARIABLE_NAME_SIGN . self::INVALID_LOCATION_VARIABLE . self::VARIABLE_NAME_SIGN;
    }

    public function getContent(): string
    {
        $originalContent = $this->getOriginalHtmlFileContent('main-contents/invalid-path-main-content.html');

        $variables = [
            'parent-directory' => $this->getFullResourcePath($this->directoryPath),
        ];
        $replacedContent = $this->getReplacedContent($originalContent, $variables);

        return $replacedContent;
    }
}
