<?php

class MainContentRouter extends Content
{
    private const TITLE_PREFIX = self::VARIABLE_NAME_SIGN . 'lang-service-name' . self::MODIFIER_SEPARATOR . self::MODIFIER_ORIGINAL . self::VARIABLE_NAME_SIGN;

    public function __construct()
    {
        parent::__construct();
    }

    public function getTitleAndContent(string $path, int $httpStatusCode): array
    {
        $object = $this->getMainContentObjectForParams($path, $httpStatusCode);
        $title = $object->getTitle(self::TITLE_PREFIX);
        $content = $object->getContent();

        return [$title, $content];
    }

    private function getMainContentObjectForParams(string $path, int $httpStatusCode): object
    {
        $stepsData = [
            'MainContentForErrors' => $httpStatusCode,
            'MainContentForDates' => $path,
            'MainContentForDataFiles' => $path,
            'MainContentForDirectories' => $path,
            'MainContentForMainPage' => $path,
        ];
        foreach ($stepsData as $class => $param) {
            $object = new $class();
            if ($object->configure($param)) {
                return $object;
            }
        }
    }
}
