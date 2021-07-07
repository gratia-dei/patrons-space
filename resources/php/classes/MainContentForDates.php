<?php

class MainContentForDates extends Content implements MainContentInterface
{
    private const MIN_YEAR_ALLOWED = 2020;
    private const MIN_MONTH_ALLOWED_IN_MIN_YEAR = 8;
    private const MAX_FUTURE_YEARS = 1;

    private $year;
    private $month;
    private $day;
    private $view;

    public function configure(string $path): bool
    {
        if (preg_match("~^/dates/(?'year'[0-9]{4})/(?'month'[0-9]{2})/(?'day'[0-9]{2})/(?'view'[-a-z0-9]+)$~", $path, $matches)) {
            $year = (int) $matches['year'];
            $month = (int) $matches['month'];
            $day = (int) $matches['day'];
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
        $originalContent = $this->getOriginalHtmlFileContent('main-content-for-dates.html');

        return $originalContent;
    }

    private function getDateString(): string
    {
        return $this->year
            . '-' . str_pad($this->month, 2, '0', STR_PAD_LEFT)
            . '-' . str_pad($this->day, 2, '0', STR_PAD_LEFT);
    }
}
