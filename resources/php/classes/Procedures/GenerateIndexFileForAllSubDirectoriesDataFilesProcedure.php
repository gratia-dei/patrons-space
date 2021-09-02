<?php

class GenerateIndexFileForAllSubDirectoriesDataFilesProcedure extends Procedure
{
    private $generatedFilesData = [];

    public function run(string $dataPath, string $nameField): void
    {
        $rootPath = $this->getFullDataPath($dataPath);

        $paths = $this->getPathTree($rootPath);
        foreach ($paths as $fullPath => $isDirectory) {
            $directoryPath = dirname($fullPath);
            $indexFilePath = $this->getIndexFilePath($directoryPath);
            $aliasFilePath = $this->getAliasFilePath($directoryPath);

            if ($isDirectory || in_array($fullPath, [
                $this->getIndexFilePath($directoryPath),
                $this->getAliasFilePath($directoryPath),
            ])) {
                continue;
            }

            $fileData = $this->getOriginalJsonFIleContentArrayForFullPath($fullPath);
            $nameData = $fileData[$nameField] ?? null;
            if (is_null($nameData)) {
                $this->error("Name field '$nameField' cannot be null for path '$fullPath'");
            }

            $names = $this->getAllMainLanguageValues($nameData);
            $fileNameWithoutExtension = $this->getFileNameWithoutExtension($fullPath);
            $this->addIndexDataElement($directoryPath, $fileNameWithoutExtension, $names);
        }

        $this->saveGeneratedFiles($this->generatedFilesData);
    }

    private function addIndexDataElement(string $directoryPath, string $baseName, array $names): void
    {
        $indexFilePath = $this->getIndexFilePath($directoryPath, true);
        $this->generatedFilesData[$indexFilePath][$baseName] = $names;
    }
}
