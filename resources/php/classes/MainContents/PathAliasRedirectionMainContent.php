<?php

class PathAliasRedirectionMainContent extends MainContent implements MainContentInterface
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
        $redirectPath = $this->getPathToRedirect($path);

        if ($redirectPath) {
            $protocol = $this->getEnvironment()->getHostProtocol();
            $host = $this->getEnvironment()->getHostDomain();
            $queryParamsString = $this->getEnvironment()->getRequestQueryParamsString();

            $this->getEnvironment()->redirect($protocol . $host . $redirectPath . $queryParamsString);
        }
    }
}
