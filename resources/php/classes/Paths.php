<?php

namespace {

    class Paths
    {
        const PROJECT_DIR = '/patrons-space/';

        public function getRootPath(bool $getDocumentRootPath = false): string
        {
            if (!$getDocumentRootPath) {
                return preg_replace('~' . self::PROJECT_DIR . '.*~', self::PROJECT_DIR, __FILE__);
            }

            return '/';
        }

        public function getResourcesPath(bool $getDocumentRootPath = false): string
        {
            return $this->getRootPath($getDocumentRootPath) . 'resources/';
        }

        public function getDataPath(bool $getDocumentRootPath = false): string
        {
            return $this->getRootPath($getDocumentRootPath) . 'data/';
        }

        public function getHtmlPath(bool $getDocumentRootPath = false): string
        {
            return $this->getResourcesPath($getDocumentRootPath) . 'html/';
        }
    }
}
