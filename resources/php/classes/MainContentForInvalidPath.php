<?php

class MainContentForInvalidPath extends Content implements MainContentInterface
{
    private const INVALID_LOCATION_VARIABLE = 'lang-invalid-location';

    private $directoryPath;

    public function configure(string $path): bool
    {
        $this->directoryPath = $this->getDataParentDirectoryPath($path);

        return true;
    }

    public function getTitle(string $prefix): string
    {
        return $prefix . ': ' . self::VARIABLE_NAME_SIGN . self::INVALID_LOCATION_VARIABLE . self::VARIABLE_NAME_SIGN;
    }

    public function getContent(): string
    {
        $originalContent = $this->getOriginalHtmlFileContent('main-content-for-invalid-path.html');

        $variables = [
            'parent-directory' => $this->directoryPath,
        ];
        $replacedContent = $this->getReplacedContent($originalContent, $variables);

        return $replacedContent;
    }
}
