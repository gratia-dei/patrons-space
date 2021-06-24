<?php

namespace {

    class Content
    {
        private $pathsObj;

        public function __construct(object $pathsObj)
        {
            $this->pathsObj = $pathsObj;
        }

        public function getContent(string $requestPath, string $httpStatusCode): string
        {
            return '#lang-comming-soon# ...';
        }
    }
}
