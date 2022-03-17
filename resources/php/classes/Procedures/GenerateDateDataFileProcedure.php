<?php

class GenerateDateDataFileProcedure extends Procedure
{
    private const PATRON_RECORD_NAME_INDEX = 'name';
    private const PATRON_RECORD_SOURCES_INDEX = 'sources';

    private const RECORDS_TREE_MODE = 'records-tree';
    private const DATES_TREE_MODE = 'dates-tree';
    private const DATES_FILE_MODE = 'dates-file';

    private const DATES_TREE_PATH_ELEMENTS_ALIASES_PATTERN = [
        '/^(0[1-9]|1[0-2])$/', //month
        '/^(0[1-9]|[1-2][0-9]|3[0-1])$/', //day
    ];

    private $dstFileData;

    public function run(string $sourceId, string $mode, string $srcPath, string $dstFilePath): void
    {
        $fullSrcPath = $this->getFullDataPath($srcPath);
        $fullDstFilePath = $this->getFullDataPath($dstFilePath);
        $fullDstFilePathWithExtension = $this->getGeneratedFileSuffix($fullDstFilePath);
        $this->dstFileData = $this->getOriginalJsonFileContentArrayForFullPath($fullDstFilePathWithExtension);

        if ($mode === self::RECORDS_TREE_MODE) {
            $this->processRecordsTreeMode($sourceId, $fullSrcPath);
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

    private function addToFileData(string $alias, string $patronUrl, string $sourceId, string $recordId): void
    {
        $data = &$this->dstFileData[$alias][$patronUrl];

        if (empty($data[self::PATRON_RECORD_NAME_INDEX] ?? [])) {
            $data[self::PATRON_RECORD_NAME_INDEX] = $this->getPatronNamesArray($patronUrl);
        }
        $data[self::PATRON_RECORD_SOURCES_INDEX][$sourceId][$recordId] = true;
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
                        $value .= ' (' . $mainNames[$language] . ')';
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
                    $this->addToFileData($alias, $patronUrl, $sourceId, $recordId);
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
}
