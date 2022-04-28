<?php

class GenerateDataLinkFilesProcedure extends Procedure
{
    private const LANGUAGE_CODE_PATTERN = '/^[a-z][a-z][a-z]?$/';
    private const POSSIBLE_NAME_INDEX = 'name';

    private $generatedFilesData = [];

    public function run(array $dataPaths, string $fieldName): void
    {
        $paths = [];
        foreach ($dataPaths as $dataPath) {
            $rootPath = $this->getFullDataPath($dataPath);

            $paths = array_merge($paths, $this->getPathTree($rootPath));
        }

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
                $this->error("file '$sourceFileFullPath' must have any '$fieldName' section");
            }

            $dataRootPath = $this->getPath()->getDataPath();
            $fileExtension = $this->getDataFileSuffix();
            $sourceFilePath = preg_replace('~^' . $dataRootPath . '(.+)' . $fileExtension . '$~U', '\1', $sourceFileFullPath);
            if ($sourceFilePath === $sourceFileFullPath) {
                $this->error("source file full path '$sourceFileFullPath' must be different than source file path '$sourceFilePath' section");
            }

            $this->addDataLinks($dataLinksData, $sourceFilePath);
        }
        $this->checkGeneratedFilesData();

        $indexedGeneratedFilesData = $this->getIndexedGeneratedFilesData($this->generatedFilesData);
        $this->saveGeneratedFiles($indexedGeneratedFilesData);
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

                    if ($dstFilePathAlias === '') {
                        $dstPathAlias = $dstDirPathAlias;
                    } else {
                        $dstPathAlias = "$dstDirPathAlias/$dstFilePathAlias";
                    }
                    $dstPath = $this->getPathToRedirect($dstPathAlias);
                    $anchor = str_replace(self::PATRON_FEASTS_PATH, '#', $fieldPath);

                    $staticFilePath = $this->getDataFileSuffix($dstPath);
                    if (!$this->dataPathExists($staticFilePath)) {
                        $this->error("cannot find static file '$staticFilePath' for file '$sourceFilePath', data-links field '$fieldPath', link '$link' and directory path alias '$dstDirPathAlias'");
                    }

                    $generatedFilePath = $this->getGeneratedFileSuffix($dstPath);
                    $generatedFileFullPath = $this->getFullDataPath($generatedFilePath);

                    $staticFileData = $this->getOriginalJsonFileContentArray($staticFilePath);
                    if (!isset($this->generatedFilesData[$generatedFileFullPath][$recordId])) {
                        $recordData = $staticFileData[$recordId][self::POSSIBLE_NAME_INDEX] ?? $staticFileData[$recordId] ?? null;
                        if (is_null($recordData)) {
                            $this->error("cannot find static file '$staticFilePath' record with ID #$recordId for file '$sourceFilePath', data-links field '$fieldPath', link '$link' and directory path alias '$dstDirPathAlias'");
                        }

                        $standardTagList = null;
                        $firstField = '';
                        foreach ($recordData as $field => $text) {
                            if (!preg_match(self::LANGUAGE_CODE_PATTERN, $field)) {
                                continue;
                            }

                            $tagList = [];
                            $textTags = $this->getTextTags($text);
                            foreach ($textTags as list($tagFull, $tagLink, $tagValue)) {
                                $tagList[$tagLink] = ($tagList[$tagLink] ?? 0) + 1;
                            }
                            ksort($tagList);

                            if (is_null($standardTagList)) {
                                $standardTagList = $tagList;
                                $firstField = $field;
                            } else if ($standardTagList !== $tagList) {
                                $this->error("there are tag list differencies between text in language '$field' and '$firstField' in static file '$staticFilePath' record with ID #$recordId for file '$sourceFilePath', data-links field '$fieldPath', link '$link' and directory path alias '$dstDirPathAlias'");
                            }
                        }

                        foreach ($standardTagList as $tagLink => $tagQuantity) {
                            if (preg_match('/^[0-9]+$/', $tagLink)) {
                                $this->generatedFilesData[$generatedFileFullPath][$recordId][$tagLink] = null;
                            }
                        }
                    }

                    if (isset($this->generatedFilesData[$generatedFileFullPath][$recordId][$linkId])) {
                        $this->error("try to override static file '$staticFilePath' record with ID #$recordId for file '$sourceFilePath', data-links field '$fieldPath', link '$link' and directory path alias '$dstDirPathAlias'");
                    }
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

    private function getIndexedGeneratedFilesData(array $generatedFilesData): array
    {
        $result = [];

        foreach ($generatedFilesData as $generatedFilePath => $data) {
            //$readData = $this->getOriginalJsonFileContentArrayForFullPath($generatedFilePath);
            //var_dump($generatedFilePath);
            //print_r($readData);
            //print_r($data);
            $result[$generatedFilePath][self::DATA_LINKS_GENERATED_FILES_INDEX] = $data;
        }

        return $result;
    }

    private function checkGeneratedFilesData(): void
    {
        foreach ($this->generatedFilesData as $generatedFilePath => $pathData) {
            foreach ($pathData as $recordId => $recordData) {
                foreach ($recordData as $linkId => $link) {
                    if (is_null($link)) {
                        $this->error("orphan link ID '$linkId' in generated file '$generatedFilePath' record with ID #$recordId");
                    }
                }
            }
        }
    }
}
