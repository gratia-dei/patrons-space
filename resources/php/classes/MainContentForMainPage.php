<?php

class MainContentForMainPage extends Content implements MainContentInterface
{
    public function configure(string $param): bool
    {
        return true;
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
