<?php

class MainContentForPathAliasRedirections extends Content implements MainContentInterface
{
    public function configure(string $path): bool
    {
        $this->redirectIfNeeded($path);

        return false;
    }

    public function getTitle(string $prefix): string
    {
        //should never be called
    }

    public function getContent(): string
    {
        //should never be called
    }

    private function redirectIfNeeded(string $path): void
    {
        //... todo
    }
}
