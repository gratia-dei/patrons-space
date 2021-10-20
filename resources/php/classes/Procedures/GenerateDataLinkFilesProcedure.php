<?php

class GenerateDataLinkFilesProcedure extends Procedure
{
    private const LANGUAGE_CODE_PATTERN = '/^[a-z][a-z][a-z]?$/';

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

        $this->saveGeneratedFiles($this->generatedFilesData);
    }

    private function addDataLinks(array $data, string $sourceFilePath): void
    {
        foreach ($data as $fieldPath => $fieldData) {
            if (!is_array($fieldData)) {
                $path = $this->getPathToRedirect($fieldData);
                if ($path === '') {
                    $this->error("invalid data-links alias path '$fieldData' for file '$sourceFilePath', data-links field '$fieldPath'");
                }

                break;
            }
            foreach ($fieldData as $dstDirPathAlias => $dataLinks) {
                foreach ($dataLinks as $link) {
                    $linkData = $this->getDataLinkElements($link);
                    if (is_null($linkData)) {
                        $this->error("invalid link '$link' in file '$sourceFilePath', data-links field '$fieldPath' and directory path alias '$dstDirPathAlias'");
                    }
                    list($linkId, $dstFilePathAlias, $recordId) = $linkData;

                    $dstPathAlias = "$dstDirPathAlias/$dstFilePathAlias";
                    $dstPath = $this->getPathToRedirect($dstPathAlias);
                    $anchor = str_replace(self::PATRON_TITLES_PATH, '#', $fieldPath);

                    $staticFilePath = $this->getDataFileSuffix($dstPath);
                    if (!$this->dataPathExists($staticFilePath)) {
                        $this->error("cannot find static file '$staticFilePath' for file '$sourceFilePath', data-links field '$fieldPath', link '$link' and directory path alias '$dstDirPathAlias'");
                    }

                    $generatedFilePath = $this->getGeneratedFileSuffix($dstPath);
                    $generatedFileFullPath = $this->getFullDataPath($generatedFilePath);

                    $staticFileData = $this->getOriginalJsonFileContentArray($staticFilePath);
                    if (!isset($this->generatedFilesData[$generatedFileFullPath][$recordId])) {
                        $recordData = $staticFileData[$recordId] ?? null;
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
                    } else if (in_array($sourceFilePath . $anchor, $this->generatedFilesData[$generatedFileFullPath][$recordId] ?? [])) {
                        $this->error("more than one different generated link IDs have same location in static file '$staticFilePath' record with ID #$recordId for file '$sourceFilePath', data-links field '$fieldPath', link '$link' and directory path alias '$dstDirPathAlias'");
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
