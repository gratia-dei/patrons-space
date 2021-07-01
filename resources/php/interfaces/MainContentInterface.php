<?php

interface MainContentInterface
{
    public function configure(array $params): bool;
    public function getTitle(): string;
    public function getContent(): string;
}
