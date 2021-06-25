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

        $variables = [
            'title' => $this->bodyContent->getTitle(),
            'body' => $this->bodyContent->getContent(),
        ];
        $replacedContent = $this->getReplacedContent($originalContent, $variables);

        $language = $this->getEnvironment()->getHostSubdomainOnly();
        $websiteTranslatedVariables = $this->getTranslatedVariables($language, 'website-language-variables.json');
        $translatedContent = $this->getReplacedContent($replacedContent, $websiteTranslatedVariables, true);
        $translatedAgainContent = $this->getReplacedContent($translatedContent, $websiteTranslatedVariables);

        return $translatedAgainContent;
    }
}
