<?php

class MainContentForDirectories extends Content implements MainContentInterface
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
        $originalContent = $this->getOriginalHtmlFileContent('main-content-for-directories.html');

        //... todo

        return $originalContent;
    }
}
