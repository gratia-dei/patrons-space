<?php

class Date
{
    public function getCurrentDateTime(): string
    {
        return date('Y-m-d H:i:s');
    }

    public function getCurrentYear(): string
    {
        return date('Y');
    }

    public function getCurrentMonth(): string
    {
        return date('m');
    }

    public function getCurrentDay(): string
    {
        return date('d');
    }

    public function isValidMonthWithDay(string $monthWithDay): bool
    {
        if (!preg_match("/^(?'month'[0-9][0-9])-(?'day'[0-9][0-9])$/", $monthWithDay, $matches)) {
            return false;
        }

        $month = (int) $matches['month'];
        if ($month < 1 || $month > 12) {
            return false;
        }

        $day = (int) $matches['day'];
        if (
            $day < 1
            || $day > 31
            || (in_array($month, [4, 6, 9, 11]) && $day > 30)
            || ($month === 2 && $day > 29)
        ) {
            return false;
        }

        return true;
    }

    public function getDateMovedByDays(string $date, int $moveDays): string
    {
        $dateTime = strtotime("$date 00:00:00");

        return date('Y-m-d', $dateTime + $moveDays * 24 * 60 * 60);
    }
}
