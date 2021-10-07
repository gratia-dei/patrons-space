<?php

class GenerateIndexAndAliasFilesForSubDirectoriesPrefixedNamedFilesProcedure extends Procedure
{
    private const STEP = 1;

    private $generatedFilesData = [];

    public function run(string $dataPath, string $defaultLanguage): void
    {
        $step = self::STEP;

        $generatedFilesSuffix = $this->getGeneratedFileSuffix();
        $dataFilesSuffix = $this->getDataFileSuffix();

        $rootPath = $this->getFullDataPath($dataPath);
        $paths = $this->getPathTree($rootPath);
        foreach ($paths as $fullPath => $isDirectory) {
            $path = trim(mb_substr($fullPath, mb_strlen($rootPath)), '/');
            if ($path === '') {
                continue;
            }

            $pathElements = explode('/', $path);
            $fileName = '';
            if (!$isDirectory) {
                $fileNameWithExtension = array_pop($pathElements);
                if (empty($pathElements)) {
                    $this->error("Static files are not allowed in root directory for path  '$fullPath'");
                } else if (mb_substr($fileNameWithExtension, -mb_strlen($dataFilesSuffix)) !== $dataFilesSuffix) {
                    $this->error("Invalid file extension for path '$fullPath'");
                }

                $fileName = mb_substr($fileNameWithExtension, 0, -mb_strlen($dataFilesSuffix));
                if (!$this->isPathElementsValid(array_merge($pathElements, [$fileName]), $step)) {
                    $this->error("Invalid path elements for path '$fullPath'");
                }

                $this->addDataToGeneratedFiles($rootPath, $pathElements, $fileName, $defaultLanguage);
            } else if (!$this->isPathElementsValid($pathElements, $step)) {
                $this->error("Invalid path elements for path '$fullPath'");
            }
        }

        $this->saveGeneratedFiles($this->generatedFilesData);
    }

    private function isPathElementsValid(array $elements, int $step): bool
    {
        $prevElement = '';
        foreach ($elements as $element) {
            if ($prevElement !== '' && mb_strpos($element, $prevElement) !== 0) {
                return false;
            } else if (mb_substr($element, 0, -1) !== $prevElement) {
                return false;
            }

            $prevElement = $element;
        }

        return true;
    }

    private function getPathForElements(string $rootPath, array $pathElements): string
    {
        return rtrim($rootPath . '/' . implode('/', $pathElements), '/');
    }

    private function addDataToGeneratedFiles(string $rootPath, array $pathElements, string $fileName, string $defaultLanguage): void
    {
        $path = $this->getPathForElements($rootPath, $pathElements);
        $indexedElement = mb_strtolower($fileName);

        $baseIndexedElement = $indexedElement;
        $baseAliasPath = implode('/', $pathElements) . '/' . $baseIndexedElement;

        do {
            $this->addIndexDataElement($path, $indexedElement, $defaultLanguage);

            $indexedElement = array_pop($pathElements);
            $path = $this->getPathForElements($rootPath, $pathElements);
        } while (!empty($pathElements));

        $this->addIndexDataElement($path, $indexedElement, $defaultLanguage);
        $this->addAliasDataElement($path, $baseIndexedElement, $baseAliasPath);
    }

    private function addIndexDataElement(string $path, string $element, string $defaultLanguage): void
    {
        $filePath = $this->getIndexFilePath($path, true);
        $this->generatedFilesData[$filePath][$element] = [$defaultLanguage => mb_strtoupper($element)];
    }

    private function addAliasDataElement(string $path, string $element, string $aliasPath): void
    {
        $filePath = $this->getAliasFilePath($path, true);
        $this->generatedFilesData[$filePath][$element] = $aliasPath;
    }
}
