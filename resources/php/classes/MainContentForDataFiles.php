<?php

class MainContentForDataFiles extends Content implements MainContentInterface
{
    public function configure(string $path): bool
    {
        //... todo

        return false;
    }

    public function getTitle(string $prefix): string
    {
        return $prefix; //... todo
    }

    public function getContent(): string
    {
        $originalContent = $this->getOriginalHtmlFileContent('main-content-for-data-files.html');

        //... todo

        return $originalContent;
    }
}
