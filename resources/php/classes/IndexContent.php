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

        list($title, $content) = $this->bodyContent->getTitleAndContent();
        $variables = [
            'title' => $title,
            'body' => $content,
        ];
        $replacedContent = $this->getReplacedContent($originalContent, $variables);

        $language = $this->getEnvironment()->getHostSubdomainOnly();
        $websiteTranslatedVariables = $this->getTranslatedVariables($language, 'website-language-variables.json');
        $translatedContent = $this->getReplacedContent($replacedContent, $websiteTranslatedVariables, true);
        $translatedAgainContent = $this->getReplacedContent($translatedContent, $websiteTranslatedVariables);

        return $translatedAgainContent;
    }
}
