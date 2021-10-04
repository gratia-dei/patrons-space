<?php

interface ContentBlockInterface
{
    public function getContent(string $path, string $fileNameTranslated): string;
}
