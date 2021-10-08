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
        $result = $this->getOriginalHtmlFileContent('main-contents/main-page-main-content.html');

        $variables = [
            'data-link' => $this->getFullResourcePath('data'),
            'my-patrons-for-today-link' => $this->getFullResourcePath('/dates/my-patrons/#current-year#/#current-month#/#current-day#'),
        ];
        $result = $this->getReplacedContent($result, $variables);

        $variables = [
            'current-year' => $this->getDate()->getCurrentYear(),
            'current-month' => $this->getDate()->getCurrentMonth(),
            'current-day' => $this->getDate()->getCurrentDay(),
        ];
        $result = $this->getReplacedContent($result, $variables);

        return $result;
    }
}
