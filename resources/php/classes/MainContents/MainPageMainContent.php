<?php

class MainPageMainContent extends MainContent implements MainContentInterface
{
    public function configure(string $path): bool
    {
        return (trim($path, '/') === '');
    }

    public function getTitle(string $prefix): string
    {
        return $prefix;
    }

    public function getContent(): string
    {
        $originalContent = $this->getOriginalHtmlFileContent('main-contents/main-page-main-content.html');

        return $originalContent;
    }
}
