<?php

class DateMainContent extends MainContent implements MainContentInterface
{
    private const DEFAULT_CONTENT_BLOCK_CLASS_NAME = 'DateOtherContentBlock';

    private const PARENT_DIRECTORY = self::DATA_ROOT_PARENT_DIRECTORY_PATH;

    private const MIN_YEAR_ALLOWED = 2020;
    private const MIN_MONTH_ALLOWED_IN_MIN_YEAR = 8;
    private const MAX_FUTURE_YEARS = 1;

    private $year;
    private $month;
    private $day;
    private $view;

    public function configure(string $path): bool
    {
        if (preg_match("~^/dates/(?'view'[-a-z0-9]+)(/(?'year'[0-9]{4})/(?'month'[0-9]{2})/(?'day'[0-9]{2}))?$~", $path, $matches)) {
            $year = (int) ($matches['year'] ?? date('Y'));
            $month = (int) ($matches['month'] ?? date('m'));
            $day = (int) ($matches['day'] ?? date('d'));
            $view = $matches['view'];

            if (checkdate($month, $day, $year)) {
                if ($year < self::MIN_YEAR_ALLOWED
                    || ($year === self::MIN_YEAR_ALLOWED && $month < self::MIN_MONTH_ALLOWED_IN_MIN_YEAR)
                    || $year > (int) date('Y') + self::MAX_FUTURE_YEARS
                ) {
                    return false;
                }

                $this->year = $year;
                $this->month = $month;
                $this->day = $day;
                $this->view = $view;

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
            'parent-directory' => $this->getFullResourcePath(self::PARENT_DIRECTORY),
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
        $path = $this->getDateString();
        $fileNameTranslated = '...fileNameTranslated';

        $class = self::DEFAULT_CONTENT_BLOCK_CLASS_NAME;
        $contentBlockViews = $this->getOriginalJsonFileContentArray('date-content-block-configuration.json');
        foreach ($contentBlockViews as $view => $classForPath) {
            if ($view === $this->view) {
                $class = $classForPath;
                break;
            }
        }

        return (new $class())->getContent($path, $fileNameTranslated);
    }
}
