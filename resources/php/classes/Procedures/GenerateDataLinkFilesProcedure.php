<?php

class GenerateDataLinkFilesProcedure extends Procedure
{
    private const ANCHOR_REPLACE_PATH = 'titles/';

    private $generatedFilesData = [];

    public function run(string $dataPath, string $fieldName): void
    {
        $rootPath = $this->getFullDataPath($dataPath);

        $paths = $this->getPathTree($rootPath);
        foreach ($paths as $sourceFileFullPath => $isDirectory) {
            if ($isDirectory) {
                continue;
            }

            $directoryPath = dirname($sourceFileFullPath);
            if (in_array($sourceFileFullPath, [
                $this->getIndexFilePath($directoryPath),
                $this->getIndexFilePath($directoryPath, true),
                $this->getAliasFilePath($directoryPath),
                $this->getAliasFilePath($directoryPath, true),
            ])) {
                continue;
            }

            $fileData = $this->getOriginalJsonFIleContentArrayForFullPath($sourceFileFullPath);
            $dataLinksData = $this->getFileDataLinks($fileData, $fieldName);
            if (empty($dataLinksData)) {
                $this->error("file '$sourceFilePath' must have any '$fieldName' section");
            }

            $dataRootPath = $this->getPath()->getDataPath();
            $fileExtension = $this->getDataFileSuffix();
            $sourceFilePath = preg_replace('~^' . $dataRootPath . '(.+)' . $fileExtension . '$~U', '\1', $sourceFileFullPath);
            if ($sourceFilePath === $sourceFileFullPath) {
                $this->error("source file full path '$sourceFileFullPath' must be different than source file path '$sourceFilePath' section");
            }

            $this->addDataLinks($dataLinksData, $sourceFilePath);
        }

        $this->saveGeneratedFiles($this->generatedFilesData);
    }

    private function addDataLinks(array $data, string $sourceFilePath): void
    {
        foreach ($data as $fieldPath => $fieldData) {
            foreach ($fieldData as $dstDirPathAlias => $dataLinks) {
                foreach ($dataLinks as $link) {
                    $linkData = $this->getDataLinkElements($link);
                    if (is_null($linkData)) {
                        $this->error("invalid link '$link' in file '$sourceFilePath', data-links field '$fieldPath' and directory path alias '$dstDirPathAlias'");
                    }
                    list($linkId, $dstFilePathAlias, $recordId) = $linkData;

                    $dstPathAlias = "$dstDirPathAlias/$dstFilePathAlias";
                    $dstPath = $this->getPathToRedirect($dstPathAlias);
                    $anchor = str_replace(self::ANCHOR_REPLACE_PATH, '#', $fieldPath);

                    $staticFilePath = $this->getDataFileSuffix($dstPath);
                    if (!$this->dataPathExists($staticFilePath)) {
                        $this->error("cannot find static file '$staticFilePath' for file '$sourceFilePath', data-links field '$fieldPath' and directory path alias '$dstDirPathAlias'");
                    }

                    $generatedFilePath = $this->getGeneratedFileSuffix($dstPath);
                    $generatedFileFullPath = $this->getFullDataPath($generatedFilePath);

                    $this->generatedFilesData[$generatedFileFullPath][$recordId][$linkId] = $sourceFilePath . $anchor;
                }
            }
        }
    }

    private function getFileDataLinks(array $data, string $fieldName, array $result = [], string $path = ''): array
    {
        foreach ($data as $field => $value) {
            if ($field === $fieldName) {
                $result[$path] = $value;
            } else if (is_array($value)) {
                $result = $this->getFileDataLinks($value, $fieldName, $result, trim("$path/$field", '/'));
            }
        }

        return $result;
    }
}
