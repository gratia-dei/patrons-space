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

    public function get1956TheGreaterLitaniesInTheChurchOfSaintPeterDate(int $year): string
    {
        //...

        return self::MISSING_DATE;
    }

    public function get2004BaptismOfJesusFeastDate(int $year): string
    {
        //...

        return self::MISSING_DATE;
    }

    public function get2004HolyFamilyFeastDate(int $year): string
    {
        //...

        return self::MISSING_DATE;
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
