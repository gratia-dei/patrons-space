<?php

namespace {

    class Paths
    {
        const PROJECT_DIR = '/patrons-space/';

        public static function getRootPath(bool $getFullPath = true): string
        {
            if ($getFullPath) {
                return preg_replace('~' . self::PROJECT_DIR . '.*~', self::PROJECT_DIR, __FILE__);
            }

            return '/';
        }

        public static function getResourcesPath(bool $getFullPath = true): string
        {
            return self::getRootPath($getFullPath) . 'resources/';
        }

        public static function getDataPath(bool $getFullPath = true): string
        {
            return self::getRootPath($getFullPath) . 'data/';
        }

        public static function getHtmlPath(bool $getFullPath = true): string
        {
            return self::getResourcesPath($getFullPath) . 'html/';
        }

        public static function getStylesPath(bool $getFullPath = true): string
        {
            return self::getResourcesPath($getFullPath) . 'css/';
        }
    }
}
