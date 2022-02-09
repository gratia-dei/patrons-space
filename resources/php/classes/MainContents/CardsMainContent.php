<?php

class CardsMainContent extends MainContent implements MainContentInterface
{
    public function configure(string $path): bool
    {
        if (preg_match("~^/cards$~", $path)) {
            return true;
        }

        return false;
    }

    public function getTitle(string $prefix): string
    {
        return self::VARIABLE_NAME_SIGN . 'lang-cards' . self::MODIFIER_SEPARATOR . self::MODIFIER_CAPITALIZE . self::VARIABLE_NAME_SIGN;
    }

    public function getContent(): string
    {
        $originalContent = $this->getOriginalHtmlFileContent('main-contents/cards-main-content.html');

        $variables = [
            //...
        ];
        $replacedContent = $this->getReplacedContent($originalContent, $variables);

        return $replacedContent;
    }
}
