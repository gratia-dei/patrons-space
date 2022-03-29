<?php

class GenerateDateDataFileProcedure extends Procedure
{
    private const RECORDS_TREE_MODE = 'records-tree';
    private const DATES_TREE_MODE = 'dates-tree';
    private const DATES_FILE_MODE = 'dates-file';

    private const DATES_TREE_PATH_ELEMENTS_ALIASES_PATTERN = [
        '/^(0[1-9]|1[0-2])$/', //month
        '/^(0[1-9]|[1-2][0-9]|3[0-1])$/', //day
    ];

    private const RECORD_TREE_SOURCE_FIELDS = [
        'reliable-death-anniversary' => self::PATRON_DIED_INDEX,
        'other-memorial-days' => self::PATRON_MENTIONED_INDEX,
    ];
    private const RECORD_TREE_METHODS = [
        'reliable-death-anniversary' => 'getDeathMonthWithDay',
        'other-memorial-days' => 'getMentionedMonthsWithDays',
    ];

    private $dstFileData;

    public function run(string $sourceId, string $mode, string $srcPath, string $dstFilePath): void
    {
        $fullSrcPath = $this->getFullDataPath($srcPath);
        $fullDstFilePath = $this->getFullDataPath($dstFilePath);
        $fullDstFilePathWithExtension = $this->getGeneratedFileSuffix($fullDstFilePath);
        $this->dstFileData = $this->getOriginalJsonFileContentArrayForFullPath($fullDstFilePathWithExtension);

        if ($mode === self::RECORDS_TREE_MODE) {
            $this->processRecordsTreeMode($sourceId, $fullSrcPath, ltrim($srcPath, '/'));
        } else if ($mode === self::DATES_TREE_MODE) {
            $this->processDatesTreeMode($sourceId, $fullSrcPath);
        } else if ($mode === self::DATES_FILE_MODE) {
            $this->processDatesFileMode($sourceId, $fullSrcPath);
        } else {
            $this->error("unknown generate date data file procedure mode '$mode'");
        }

        ksort($this->dstFileData);
        $this->saveGeneratedFiles([$fullDstFilePathWithExtension => $this->dstFileData], true);
    }

    private function addToFileData(string $alias, string $patronUrl, string $sourceId): void
    {
        $data = &$this->dstFileData[$alias][$patronUrl];

        if (empty($data[self::DATES_DATA_PATRON_RECORD_NAME_INDEX] ?? [])) {
            $data[self::DATES_DATA_PATRON_RECORD_NAME_INDEX] = $this->getPatronNamesArray($patronUrl);
        }
        $data[self::DATES_DATA_PATRON_RECORD_SOURCES_INDEX][$sourceId] = true;
    }

    private function getPatronNamesArray(string $patronUrl): array
    {
        $result = [];

        if (preg_match("/^(?'path'[^#]+)([#](?'feast'[0-9]+))?$/", $patronUrl, $matches)) {
            $filePath = $this->getDataFileSuffix($matches['path']);
            $feastId = $matches['feast'] ?? null;

            $data = $this->getOriginalJsonFileContentArray($filePath);

            $mainNames = [];
            foreach ($data[self::PATRON_NAMES_INDEX] ?? [] as $language => $values) {
                $mainNames[$language] = $values[0];
            }
            if (!is_null($feastId)) {
                foreach ($data[self::PATRON_FEASTS_INDEX][$feastId][self::PATRON_NAMES_INDEX] ?? [] as $language => $values) {
                    $value = $values[0];
                    if (isset($mainNames[$language])) {
                        $value = $mainNames[$language] . ' (' . $value . ')';
                    }
                    $result[$language] = $value;
                }
            } else {
                $result = $mainNames;
            }
        }

        if (empty($result)) {
            $this->error("Missing patron's name for URL '$patronUrl'");
        }

        return $result;
    }

    private function processDatesTreeMode(string $sourceId, string $srcPath): void
    {
        $aliases = $this->getDatesTreeModeAliases($srcPath);
        $paths = $this->getPathTree($srcPath);
        foreach ($paths as $path => $isDirectory) {
            if ($isDirectory) {
                continue;
            }

            $subPath = ltrim(mb_substr($path, mb_strlen($srcPath)), '/');
            $subPathElementsCount = count(explode('/', $subPath));
            if ($subPathElementsCount !== 2) {
                continue;
            }

            $dirName = dirname($subPath);
            $fileName = basename($subPath);
            $fileNameWithoutExtension = explode('.', $fileName)[0] ?? '';

            $monthAlias = $aliases['.'][$dirName] ?? null;
            $dayAlias = null;
            if (preg_match(self::DATES_TREE_PATH_ELEMENTS_ALIASES_PATTERN[1], $fileNameWithoutExtension)) {
                $dayAlias = $fileNameWithoutExtension;
            } else {
                $dayAlias = $aliases[$dirName][$fileNameWithoutExtension] ?? null;
            }
            if (is_null($monthAlias) || is_null($dayAlias)) {
                continue;
            }

            $alias = "$monthAlias-$dayAlias";
            $fileData = $this->getOriginalJsonFileContentArrayForFullPath($path)[self::DATA_LINKS_GENERATED_FILES_INDEX] ?? [];
            if (empty($fileData)) {
                continue;
            }

            foreach ($fileData as $recordId => $recordData) {
                foreach ($recordData as $patronUrl) {
                    $this->addToFileData($alias, $patronUrl, $sourceId);
                }
            }
        }
    }

    private function getDatesTreeModeAliases(string $srcPath): array
    {
        $aliases = [];

        $paths = $this->getPathTree($srcPath);
        foreach ($paths as $path => $isDirectory) {
            $aliasPath = $this->getAliasFilePath(dirname($path));
            if ($path !== $aliasPath) {
                continue;
            }

            $subPath = ltrim(mb_substr($path, mb_strlen($srcPath)), '/');
            $aliasPattern = self::DATES_TREE_PATH_ELEMENTS_ALIASES_PATTERN[count(explode('/', $subPath)) - 1] ?? null;
            if (is_null($aliasPattern)) {
                continue;
            }

            $dirName = dirname($subPath);
            $aliasesFileData = $this->getOriginalJsonFileContentArrayForFullPath($path);
            foreach ($aliasesFileData as $alias => $target) {
                if (preg_match($aliasPattern, $alias)) {
                    $aliases[$dirName][$target] = $alias;
                }
            }
        }

        return $aliases;
    }

    private function processRecordsTreeMode(string $sourceId, string $fullSrcPath, string $patronUrlRootPath): void
    {
        $paths = $this->getPathTree($fullSrcPath);
        foreach ($paths as $path => $isDirectory) {
            if ($isDirectory) {
                continue;
            }

            $fileName = basename($path);
            $fileNameWithoutExtension = explode('.', $fileName)[0] ?? '';
            $subPath = ltrim(mb_substr($path, mb_strlen($fullSrcPath)), '/');
            $patronUrl = dirname("$patronUrlRootPath/$subPath") . "/$fileNameWithoutExtension";

            if (!preg_match('/^[0-9]+$/', $fileNameWithoutExtension)) {
                continue;
            }

            $sourceField = self::RECORD_TREE_SOURCE_FIELDS[$sourceId] ?? null;
            if (is_null($sourceField)) {
                $this->error("record tree source field not defined for source ID '$sourceId'");
            }

            $method = self::RECORD_TREE_METHODS[$sourceId] ?? null;
            if (is_null($method)) {
                $this->error("record tree method not defined for source ID '$sourceId'");
            }

            $fileData = $this->getOriginalJsonFileContentArrayForFullPath($path) ?? [];

            $monthsWithDays = $this->$method($fileData[$sourceField] ?? []);
            foreach ($monthsWithDays as $monthWithDay) {
                $this->addToFileData($monthWithDay, $patronUrl, $sourceId);
            }
        }
    }

    private function getDeathMonthWithDay(array $dates): array
    {
        $showMonthAndDay = true;
        $prevMonthAndDay = null;

        foreach ($dates as $date) {
            $monthAndDay = preg_replace('/^.+(-[0-1][0-9]-[0-3][0-9])$/', '\\1', $date);

            if ($showMonthAndDay) {
                if ($monthAndDay !== $date) {
                    if ($prevMonthAndDay === null) {
                        $prevMonthAndDay = $monthAndDay;
                    } else if ($prevMonthAndDay !== $monthAndDay) {
                        $showMonthAndDay = false;
                    }
                } else {
                    $showMonthAndDay = false;
                }
            }
        }

        if (!$showMonthAndDay || is_null($prevMonthAndDay)) {
            return [];
        }

        return [ltrim($prevMonthAndDay, '-')];
    }

    private function getMentionedMonthsWithDays(array $days): array
    {
        $result = [];

        foreach ($days as $day) {
            if ($this->getDate()->isValidMonthWithDay($day)) {
                $result[] = $day;
            }
        }

        return $result;
    }
}
