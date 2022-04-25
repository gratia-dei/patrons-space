<?php

class MovableFeastBase
{
    private const MOVABLE_RESURRECTION_FILE_DATA_PATH = 'movable-resurrection-dates.json';

    private const MISSING_DATE = '00-00';

    private $path;
    private $file;
    private $json;

    public function __construct()
    {
        $this->path = new Path();
        $this->file = new File();
        $this->json = new Json();
    }

    public function get1956HolyFamilyFeastDate(int $year): string
    {
        //...

        return self::MISSING_DATE;
    }

    public function get1956JesusTheKingFeastDate(int $year): string
    {
        //...

        return self::MISSING_DATE;
    }

    public function get1956TheMostHolyNameOfJesusFeastDate(int $year): string
    {
        //...

        return self::MISSING_DATE;
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
        //...

        return self::MISSING_DATE;
    }

    public function getResurrectionFeastDate(int $year): string
    {
        $filePath = $this->path->getDataPath(self::MOVABLE_RESURRECTION_FILE_DATA_PATH);
        $fileContent = $this->file->getFileContent($filePath);
        $fileData = $this->json->decode($fileContent);

        return $fileData[$year] ?? self::MISSING_DATE;
    }
}
