<?php

class IndexContent extends Content
{
    private $bodyContent;

    public function __construct()
    {
        parent::__construct();
        $this->bodyContent = new BodyContent();
    }

    public function getContent(): string
    {
        $originalContent = $this->getOriginalHtmlFileContent('index.html');
        $language = $this->getEnvironment()->getHostSubdomainOnly();
        $websiteTranslatedVariables = $this->getTranslatedVariables($language, 'website-language-variables.json');

        list($title, $content) = $this->bodyContent->getTitleAndContent();
        $variables = [
            'title' => $this->getReplacedContent($title, $websiteTranslatedVariables),
            'body' => $content,
        ];
        $replacedContent = $this->getReplacedContent($originalContent, $variables);

        $translatedContent = $this->getReplacedContent($replacedContent, $websiteTranslatedVariables, true);
        $finallyTranslatedContent = $this->getFinallyTranslatedContent($translatedContent, $websiteTranslatedVariables);

        return $finallyTranslatedContent;
    }
}
