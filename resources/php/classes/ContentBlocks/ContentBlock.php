<?php

abstract class ContentBlock extends Content
{
    protected const UNKNOWN_PAGE_NUMBER = self::VARIABLE_NAME_SIGN . 'unknown' . self::VARIABLE_NAME_SIGN;
    protected const UNKNOWN_PAGE_COLUMN_NUMBER = self::VARIABLE_NAME_SIGN . 'unknown' . self::VARIABLE_NAME_SIGN;

    protected const NON_EXISTENCE = self::VARIABLE_NAME_SIGN . 'lang-non-existence' . self::VARIABLE_NAME_SIGN;
    protected const UNKNOWN_SIGN = '???';
    protected const INVALID_SIGN = '!!!';

    protected const RECORD_ACTIVENESS_CLASS_ACTIVE = 'record-active';
    protected const RECORD_ACTIVENESS_CLASS_INACTIVE = 'record-inactive';

    private const EMPTY_TEXT_SPECIAL_TAG = '[...]';

    private const DATE_REPLACES = [
        '/^([<>~])?([-]?[0-9]*)[-]([0-9][0-9])[-]([0-9][0-9])$/' => '\1\4.\3.\2',
        '/[-]([0-9]+)/' => '\1 ' . self::VARIABLE_NAME_SIGN . 'lang-before-christ-abbreviation' . self::VARIABLE_NAME_SIGN,
        '/[<]/' => self::VARIABLE_NAME_SIGN . 'lang-before' . self::VARIABLE_NAME_SIGN . ' ',
        '/[>]/' => self::VARIABLE_NAME_SIGN . 'lang-after' . self::VARIABLE_NAME_SIGN . ' ',
        '/[~]/' => self::VARIABLE_NAME_SIGN . 'lang-around' . self::VARIABLE_NAME_SIGN . ' ',
    ];

    protected function getFormattedDate(string $date): string
    {
        if ($date === '-') {
            return self::NON_EXISTENCE;
        } else if ($date === '?') {
            return self::UNKNOWN_SIGN;
        } else if (!preg_match('/^[<>~]?[-]?[1-9][0-9]?[0-9]?[0-9]?([-](0[1-9]|1[0-2])[-](0[1-9]|[1-2][0-9]|3[0-1]))?$/', $date)) {
            return self::INVALID_SIGN;
        }

        foreach (self::DATE_REPLACES as $pattern => $replacement) {
            $date = preg_replace($pattern, $replacement, $date);
        }

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

    protected function getArrayIndexedFrom1(array $array): array
    {
        $result = [];

        $elementId = 0;
        foreach ($array as $value) {
            $elementId++;
            $result[$elementId] = $value;
        }

        return $result;
    }

    protected function getRecordActivenessClass(string $recordId): string
    {
        $activeRecordId = $this->getActiveRecordId();
        if ($activeRecordId === $recordId) {
            $class = self::RECORD_ACTIVENESS_CLASS_ACTIVE;
        } else {
            $class = self::RECORD_ACTIVENESS_CLASS_INACTIVE;
        }

        return $class;
    }

    protected function getTextWithSpecialLinks(string $text, array $aliases): string
    {
        if ($text === '') {
            return self::EMPTY_TEXT_SPECIAL_TAG;
        }

        $textTags = $this->getTextTags($text);
        foreach ($textTags as list($tag, $link, $value)) {
            if (preg_match('/^[1-9][0-9]*$/', $link)) {
                $link = $aliases[$link] ?? null;

                if (is_null($link)) {
                    $replacement = $value;
                } else {
                    $link = $this->getLinkWithActiveRecordIdForAnchor('/' . ltrim($link, '/'));
                    $link = $this->getRecordIdPathWithNameExtension($link, $value);
                    $replacement = '<a href="' . $link . '">' . $value . '</a>';
                }
            } else {
                $link = $this->getLinkWithActiveRecordIdForAnchor('/' . ltrim($link, '/'));
                $link = $this->getRecordIdPathWithNameExtension($link, $value);
                $replacement = '<a href="' . $link . '">' . $value . '</a>';
            }

            $text = str_replace($tag, $replacement, $text);
        }

        return $text;
    }
}
