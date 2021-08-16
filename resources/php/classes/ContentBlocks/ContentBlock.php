<?php

abstract class ContentBlock extends Content
{
    protected const UNKNOWN_PAGE_NUMBER = self::VARIABLE_NAME_SIGN . 'unknown' . self::VARIABLE_NAME_SIGN;
    protected const UNKNOWN_PAGE_COLUMN_NUMBER = self::VARIABLE_NAME_SIGN . 'unknown' . self::VARIABLE_NAME_SIGN;

    protected const NON_EXISTENCE = self::VARIABLE_NAME_SIGN . 'lang-non-existence' . self::VARIABLE_NAME_SIGN;
    protected const UNKNOWN_SIGN = '???';

    protected function getFormattedDate(string $date): string
    {
        return $date;
    }

    protected function getFormattedDates($dates)
    {
        if (!is_array($dates)) {
            return $this->getFormattedDate((string) $dates);
        }

        foreach ($dates as $key => $date) {
            $dates[$key] = $this->getFormattedDate($date);
        }

        return $dates;
    }
}
