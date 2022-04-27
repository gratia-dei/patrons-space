<?php

class MovableFeastBase
{
    private const MOVABLE_RESURRECTION_FILE_DATA_PATH = 'movable-resurrection-dates.json';

    private const MISSING_DATE = '00-00';

    private $date;
    private $path;
    private $file;
    private $json;

    public function __construct()
    {
        $this->date = new Date();
        $this->path = new Path();
        $this->file = new File();
        $this->json = new Json();
    }

    public function get1956HolyFamilyFeastDate(int $year): string
    {
        $jan7 = "$year-01-07";
        $weekDay = (int) date('w', strtotime($jan7));

        if ($weekDay === 0) {
            $weekDay = 7;
        }

        $date = $this->date->getDateMovedByDays($jan7, 7 - $weekDay);

        return substr($date, 5);
    }

    public function get1956JesusTheKingFeastDate(int $year): string
    {
        //...

        return self::MISSING_DATE;
    }

    public function get1956TheMostHolyNameOfJesusFeastDate(int $year): string
    {
        $jan2 = "$year-01-02";
        $weekDay = (int) date('w', strtotime($jan2));

        $moveDays = 0;
        if ($weekDay >= 4 && $weekDay <= 6) {
            $moveDays = 7 - $weekDay;
        }

        $date = $this->date->getDateMovedByDays($jan2, $moveDays);

        return substr($date, 5);
    }

    public function get1956TheGreaterProcessionToSaintPeterDate(int $year): string
    {
        $result = self::MISSING_DATE;

        $resurrectionDate = $this->getResurrectionFeastDate($year);
        if ($resurrectionDate === '04-25') {
            $result = '04-27';
        }

        return $result;
    }

    public function get1956VariationsTheGreaterProcessionToSaintPeterDate(int $year): string
    {
        $result = self::MISSING_DATE;

        $resurrectionDate = $this->getResurrectionFeastDate($year);
        if ($resurrectionDate === '04-25') {
            $result = '04-28';
        } else if ($resurrectionDate === '04-24') {
            $result = '04-27';
        }

        return $result;
    }

    public function get2004BaptismOfJesusFeastDate(int $year): string
    {
        $jan6 = "$year-01-06";
        $weekDay = (int) date('w', strtotime($jan6));

        $date = $this->date->getDateMovedByDays($jan6, 7 - $weekDay);

        return substr($date, 5);
    }

    public function get2004HolyFamilyFeastDate(int $year): string
    {
        $dec29 = "$year-12-29";
        $weekDay = (int) date('w', strtotime($dec29));

        $moveDays = 0;
        if ($weekDay === 4 || $weekDay === 5) {
            $moveDays = $weekDay - 3;
        } else if ($weekDay >= 1 && $weekDay <= 3) {
            $moveDays = -$weekDay;
        }

        $date = $this->date->getDateMovedByDays($dec29, $moveDays);

        return substr($date, 5);
    }

    public function getFirstSundayOfAdventDate(int $year): string
    {
        $dec25 = "$year-12-25";
        $weekDay = (int) date('w', strtotime($dec25));

        if ($weekDay === 0) {
            $weekDay = 7;
        }

        $date = $this->date->getDateMovedByDays($dec25, -4 * 7 + (7 - $weekDay));

        return substr($date, 5);
    }

    public function getResurrectionFeastDate(int $year): string
    {
        $filePath = $this->path->getDataPath(self::MOVABLE_RESURRECTION_FILE_DATA_PATH);
        $fileContent = $this->file->getFileContent($filePath);
        $fileData = $this->json->decode($fileContent);

        return $fileData[$year] ?? self::MISSING_DATE;
    }
}
