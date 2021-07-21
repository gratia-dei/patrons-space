<?php

class ErrorMainContent extends MainContent implements MainContentInterface
{
    private const NONE_STATUS_CODE = 0;
    private const OTHER_STATUS_CODE = self::NONE_STATUS_CODE;
    private const VALID_STATUS_CODE = 200;

    private const HTTP_STATUSES_DATA = [
        403 => 'lang-error-forbidden',
        404 => 'lang-error-not-found',
        self::OTHER_STATUS_CODE => 'lang-error-other',
    ];

    private $statusCode;

    public function configure(string $param): bool
    {
        $statusCode = (int) $param;
        if ($statusCode !== self::VALID_STATUS_CODE) {
            $this->statusCode = $statusCode;

            return true;
        }

        return false;
    }

    public function getTitle(string $prefix): string
    {
        $statusCode = $this->statusCode;
        $variableName = self::HTTP_STATUSES_DATA[$statusCode]
            ?? self::HTTP_STATUSES_DATA[self::OTHER_STATUS_CODE];

        return $prefix . ': ' . self::VARIABLE_NAME_SIGN . $variableName . self::VARIABLE_NAME_SIGN;
    }

    public function getContent(): string
    {
        $originalContent = $this->getOriginalHtmlFileContent('main-contents/error-main-content.html');

        $statusCode = $this->statusCode;
        $variableName = self::HTTP_STATUSES_DATA[$statusCode]
            ?? self::HTTP_STATUSES_DATA[self::OTHER_STATUS_CODE];

        $variables = ['error' => self::VARIABLE_NAME_SIGN . $variableName . self::VARIABLE_NAME_SIGN];
        $translatedContent = $this->getReplacedContent($originalContent, $variables);

        return $translatedContent;
    }
}
