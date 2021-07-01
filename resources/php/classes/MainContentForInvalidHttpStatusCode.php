<?php

class MainContentForInvalidHttpStatusCode extends Content implements MainContentInterface
{
    public const PARAMS_KEY = 'status-code';

    private const NONE_STATUS_CODE = 0;
    private const OTHER_STATUS_CODE = self::NONE_STATUS_CODE;
    private const VALID_STATUS_CODE = 200;

    private const HTTP_STATUSES_DATA = [
        403 => 'lang-error-forbidden',
        404 => 'lang-error-not-found',
        self::OTHER_STATUS_CODE => 'lang-error-other',
    ];

    private $statusCode;

    public function configure($params): bool
    {
        $statusCode = $params[self::PARAMS_KEY] ?? self::NONE_STATUS_CODE;
        if ($statusCode !== self::VALID_STATUS_CODE) {
            $this->statusCode = $statusCode;

            return true;
        }

        return false;
    }

    public function getTitle(): string
    {
        $statusCode = $this->statusCode;
        $variableName = self::HTTP_STATUSES_DATA[$statusCode]
            ?? self::HTTP_STATUSES_DATA[self::OTHER_STATUS_CODE];

        return self::VARIABLE_NAME_SIGN . $variableName . self::VARIABLE_NAME_SIGN;
    }

    public function getContent(): string
    {
        $originalContent = $this->getOriginalHtmlFileContent('main-content-for-invalid-http-status-code.html');

        $statusCode = $this->statusCode;
        $variableName = self::HTTP_STATUSES_DATA[$statusCode]
            ?? self::HTTP_STATUSES_DATA[self::OTHER_STATUS_CODE];

        $variables = ['error' => self::VARIABLE_NAME_SIGN . $variableName . self::VARIABLE_NAME_SIGN];
        $translatedContent = $this->getReplacedContent($originalContent, $variables);

        return $translatedContent;
    }
}
