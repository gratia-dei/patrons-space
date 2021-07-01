<?php

class MainContent extends Content
{
    private const DEFAULT_TITLE = self::VARIABLE_NAME_SIGN . 'lang-service-name' . self::MODIFIER_SEPARATOR . self::MODIFIER_ORIGINAL . self::VARIABLE_NAME_SIGN;
    private const DEFAULT_CONTENT = self::VARIABLE_NAME_SIGN . 'lang-comming-soon' . self::VARIABLE_NAME_SIGN;

    public function __construct()
    {
        parent::__construct();
    }

    public function getTitleAndContent(string $path, int $httpStatusCode): array
    {
        $title = self::DEFAULT_TITLE;
        $content = self::DEFAULT_CONTENT;

        $object = $this->getMainContentObjectForParams($path, $httpStatusCode);
        if ($object) {
            $title .= ': ' . $object->getTitle();
            $content = $object->getContent();
        }

        return [$title, $content];
    }

    private function getMainContentObjectForParams(string $path, int $httpStatusCode): ?object
    {
        $object = null;

        //... todo

        return $object;
    }
}
