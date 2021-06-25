<?php

class MainContent extends Content
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getTitle(string $requestPath): string
    {
        return '#lang-service-name|original#';
    }

    public function getContent(string $requestPath, string $httpStatusCode): string
    {
        return '#lang-comming-soon#';
    }
}
