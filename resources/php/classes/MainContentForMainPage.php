<?php

class MainContentForMainPage extends Content implements MainContentInterface
{
    public function configure(string $path): bool
    {
        return ($path === '');
    }

    public function getTitle(string $prefix): string
    {
        return $prefix;
    }

    public function getContent(): string
    {
        $originalContent = $this->getOriginalHtmlFileContent('main-content-for-main-page.html');

        return $originalContent;
    }
}
