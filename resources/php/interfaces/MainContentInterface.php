<?php

interface MainContentInterface
{
    public function configure(string $param): bool;
    public function getTitle(string $prefix): string;
    public function getContent(): string;
}
