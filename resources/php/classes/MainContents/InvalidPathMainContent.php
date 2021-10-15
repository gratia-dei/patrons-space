<?php

class InvalidPathMainContent extends MainContent implements MainContentInterface
{
    private const INVALID_LOCATION_VARIABLE = 'lang-invalid-location';

    public function configure(string $path): bool
    {
        return true;
    }

    public function getTitle(string $prefix): string
    {
        return $prefix . ': ' . self::VARIABLE_NAME_SIGN . self::INVALID_LOCATION_VARIABLE . self::VARIABLE_NAME_SIGN;
    }

    public function getContent(): string
    {
        return $this->getOriginalHtmlFileContent('main-contents/invalid-path-main-content.html');
    }
}
