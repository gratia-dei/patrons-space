<?php

class DateMainContent extends MainContent implements MainContentInterface
{
    private const MIN_YEAR_ALLOWED = 1901;
    private const MAX_FUTURE_YEARS = 20;

    private $year;
    private $month;
    private $day;
    private $view;
    private $contentBlockClass;

    public function configure(string $path): bool
    {
        if (preg_match("~^/dates/(?'view'[-a-z0-9]+)(/(?'year'[0-9]{4})/(?'month'[0-9]{2})/(?'day'[0-9]{2}))?$~", $path, $matches)) {
            $year = (int) ($matches['year'] ?? date('Y'));
            $month = (int) ($matches['month'] ?? date('m'));
            $day = (int) ($matches['day'] ?? date('d'));
            $view = $matches['view'];

            if (checkdate($month, $day, $year)) {
                if ($year < self::MIN_YEAR_ALLOWED
                    || $year > (int) date('Y') + self::MAX_FUTURE_YEARS
                ) {
                    return false;
                }

                $contentBlockViews = $this->getOriginalJsonFileContentArray('date-content-block-configuration.json');
                $contentBlockClass = $contentBlockViews[$view] ?? null;
                if (is_null($contentBlockClass)) {
                    return false;
                }

                $this->year = $year;
                $this->month = $month;
                $this->day = $day;
                $this->view = $view;
                $this->contentBlockClass = $contentBlockClass;

                return true;
            }
        }

        return false;
    }

    public function getTitle(string $prefix): string
    {
        return $prefix . ': ' . $this->getDateString();
    }

    public function getContent(): string
    {
        $originalContent = $this->getOriginalHtmlFileContent('main-contents/date-main-content.html');

        $variables = [
            'content' => $this->getDateFileContent(),
        ];
        $replacedContent = $this->getReplacedContent($originalContent, $variables);

        return $replacedContent;
    }

    private function getDateString(): string
    {
        return $this->year
            . '-' . str_pad($this->month, 2, '0', STR_PAD_LEFT)
            . '-' . str_pad($this->day, 2, '0', STR_PAD_LEFT);
    }

    private function getDateFileContent(): string
    {
        $date = $this->getDateString();
        $nameTranslated = '';

        return (new $this->contentBlockClass())->prepare($date)->getFullContent($nameTranslated);
    }
}
