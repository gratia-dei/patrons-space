<?php

interface ContentBlockInterface
{
    public function prepare(string $path): ContentBlock;
    public function getFullContent(string $translatedName): string;
    public function getRecordContent(string $recordId): string;
}
