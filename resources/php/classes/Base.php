<?php

abstract class Base
{
    private $date;
    private $environment;
    private $file;
    private $json;
    private $path;

    protected const PATRON_NAMES_INDEX = 'names';
    protected const PATRON_FEASTS_INDEX = 'feasts';
    protected const PATRON_DIED_INDEX = 'died';
    protected const PATRON_MENTIONED_INDEX = 'also-mentioned';
    protected const PATRON_FEASTS_PATH = self::PATRON_FEASTS_INDEX . '/';

    protected const DATES_DATA_PATRON_RECORD_NAME_INDEX = 'name';
    protected const DATES_DATA_PATRON_RECORD_SOURCES_INDEX = 'sources';

    protected const GENERATED_FILE_NAME_SUFFIX = '.generated';
    protected const DATA_FILE_EXTENSION = '.json';

    protected const LANG_VARIABLE_PREFIX = 'lang-';

    protected const DATA_LINKS_GENERATED_FILES_INDEX = 'data-links';

    private const RECORD_ID_WITH_NAME_EXTENSION_SEPARATOR = '--';
    private const RECORD_ID_NAME_EXTENSION_CHARACTERS_MAPPING = [
        ' ' => '-',
        '/' => '',
        '?' => '',
        '#' => '',
        '.' => '',
        ',' => '',
    ];

    public function __construct()
    {
        $this->date = new Date();
        $this->environment = new Environment();
        $this->file = new File();
        $this->json = new Json();
        $this->path = new Path();
    }

    protected function getDate(): Date
    {
        return $this->date;
    }

    protected function getEnvironment(): Environment
    {
        return $this->environment;
    }

    protected function getFile(): File
    {
        return $this->file;
    }

    protected function getJson(): Json
    {
        return $this->json;
    }

    protected function getPath(): Path
    {
        return $this->path;
    }

    protected function getOriginalJsonFileContentArrayForFullPath(string $jsonFilePath): array
    {
        $content = $this->getFile()->getFileContent($jsonFilePath);

        return $this->getJson()->decode($content);
    }

    protected function getOriginalJsonFileContentArray(string $jsonFileName): array
    {
        $jsonPath = $this->getPath()->getDataPath($jsonFileName);

        return $this->getOriginalJsonFileContentArrayForFullPath($jsonPath);
    }

    protected function getDataFileSuffix(string $path = ''): string
    {
        return $path . self::DATA_FILE_EXTENSION;
    }

    protected function getGeneratedFileSuffix(string $path = ''): string
    {
        return $path . self::GENERATED_FILE_NAME_SUFFIX . $this->getDataFileSuffix();
    }

    protected function getIndexFilePath(string $path, bool $forGeneratedFile = false): string
    {
        return $path . '/index' . ($forGeneratedFile ? self::GENERATED_FILE_NAME_SUFFIX : '') . self::DATA_FILE_EXTENSION;
    }

    protected function getAliasFilePath(string $path, bool $forGeneratedFile = false): string
    {
        return $path . '/alias' . ($forGeneratedFile ? self::GENERATED_FILE_NAME_SUFFIX : '') . self::DATA_FILE_EXTENSION;
    }

    protected function getPathToRedirect(string $originalPath): string
    {
        $path = $this->getRequestPathRecordIdOnly($originalPath);
        $nameExtension = mb_substr($originalPath, mb_strlen($path));

        if ($this->dataPathExists($path) || $this->dataPathExists($path . self::DATA_FILE_EXTENSION)) {
            return '';
        }

        $wasPathChanged = false;
        $pathElements = explode('/', $path);
        $pathCount = count($pathElements);
        for ($element = 1; $element <= $pathCount; $element++) {
            $tmpPath = implode('/', array_slice($pathElements, 0, $element));
            $basename = $pathElements[$element - 1];

            if (!$this->dataPathExists($tmpPath)) {
                $aliasFilePath = $this->getAliasFilePath(dirname($tmpPath));
                if (!$this->dataPathExists($aliasFilePath)) {
                    $aliasFilePath = $this->getAliasFilePath(dirname($tmpPath), true);
                    if (!$this->dataPathExists($aliasFilePath)) {
                        break;
                    }
                }

                $aliasData = $this->getOriginalJsonFileContentArray($aliasFilePath);
                if (!isset($aliasData[$basename])) {
                    break;
                }

                if ($basename !== $aliasData[$basename]) {
                    $pathElements[$element - 1] = $aliasData[$basename];
                    $wasPathChanged = true;
                }
            }
        }

        if ($wasPathChanged) {
            $path = implode('/', $pathElements);
            if ($this->dataPathExists($path) || $this->dataPathExists($path . self::DATA_FILE_EXTENSION)) {
                $result = preg_replace('~[/]+~', '/', '/' . $path);

                if (preg_match('~/[0-9]+$~', $path)) {
                    return $result . $nameExtension;
                }

                return $result;
            }
        }

        return '';
    }

    protected function dataPathExists(string $path): bool
    {
        $dataPath = $this->getPath()->getDataPath($path);

        return $this->getFile()->exists($dataPath);
    }

    protected function getDataLinkElements(string $link): ?array
    {
        if (!preg_match("/^(?'link_id'[1-9][0-9]*)[:](?'path'[^# ]*)[#](?'record_id'[1-9][0-9]*)$/", $link, $matches)) {
            return null;
        }

        $linkId = (int) $matches['link_id'];
        $path = $matches['path'];
        $recordId = (int) $matches['record_id'];

        return [$linkId, $path, $recordId];
    }

    protected function getTextTags(string $text): array
    {
        $result = [];

        preg_match_all("/\[(?'link'[^|]+)[|](?'value'[^|]+)\]/U", $text, $matches);
        foreach ($matches[0] ?? [] as $key => $tag) {
            $value = $matches['value'][$key];
            $link = $matches['link'][$key];

            $result[$key] = [$tag, $link, $value];
        }

        return $result;
    }

    protected function getRequestPathRecordIdOnly(string $requestPath): string
    {
        return preg_replace('~(/[0-9]+)' . self::RECORD_ID_WITH_NAME_EXTENSION_SEPARATOR . '[^/]*$~', '\1', $requestPath);
    }

    protected function getRecordIdPathWithNameExtension(string $path, string $name): string
    {
        $charsFrom = array_keys(self::RECORD_ID_NAME_EXTENSION_CHARACTERS_MAPPING);
        $charsTo = array_values(self::RECORD_ID_NAME_EXTENSION_CHARACTERS_MAPPING);

        $extension = $name;
        $extension = $this->stripTags($extension);
        $extension = mb_strtolower($extension);
        $extension = str_replace($charsFrom, $charsTo, $extension);
        $extension = urlencode($extension);

        return preg_replace(
            '~(/[0-9]+)([?#].*)?$~',
            '\1' . self::RECORD_ID_WITH_NAME_EXTENSION_SEPARATOR . $extension . '\2',
            $path
        );
    }
}
