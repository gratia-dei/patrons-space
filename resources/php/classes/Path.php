<?php

class Path
{
    private $environment;

    public function __construct()
    {
        $this->environment = new Environment();
    }

    public function getDataPath(string $subPath = '', bool $getDocumentRootPath = false): string
    {
        return $this->getRootPath('data/' . $subPath, $getDocumentRootPath);
    }

    public function getHtmlPath(string $subPath = '', bool $getDocumentRootPath = false): string
    {
        return $this->getResourcesPath('html/' . $subPath, $getDocumentRootPath);
    }

    private function getRootPath(string $subPath = '', bool $getDocumentRootPath = false): string
    {
        if (!$getDocumentRootPath) {
            $rootPath = $this->environment->getRootDirectoryPath();
        } else {
            $rootPath = '/';
        }

        return $rootPath . $subPath;
    }

    private function getResourcesPath(string $subPath = '', bool $getDocumentRootPath = false): string
    {
        return $this->getRootPath('resources/' . $subPath, $getDocumentRootPath);
    }
}
