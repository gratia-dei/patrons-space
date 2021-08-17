<?php

class Environment
{
    private const DOMAIN_PATTERN = '/patrons([.][a-z]+)?[.]space$/';
    private const ROOT_DIRECTORY_NAME = 'patrons-space';

    public function __construct()
    {
    }

    public function getRootDirectoryPath(): string
    {
        $rootDirectoryPath = '/' . self::ROOT_DIRECTORY_NAME . '/';
        $classPath = $this->getEnvironmentClassPath();

        return preg_replace('~' . $rootDirectoryPath . '.*~', $rootDirectoryPath, $classPath);
    }

    public function getRequestPath(): string
    {
        $requestPath = $this->getFromServerGlobal('REQUEST_URI');

        return '/' . $this->getTidyPath($requestPath);
    }

    public function getHttpStatusCode(): int
    {
        return (int) $this->getFromServerGlobal('REDIRECT_STATUS');
    }

    public function getHostProtocol(): string
    {
        return $this->getFromServerGlobal('HTTPS') ? 'https://' : 'http://';
    }

    public function getHostDomain(): string
    {
        return $this->getFromServerGlobal('SERVER_NAME');
    }

    public function getHostMainDomainOnly(): string
    {
        $host = $this->getHostDomain();
        $subdomainOnly = $this->getHostSubdomainOnly();

        return ltrim(mb_substr($host, mb_strlen($subdomainOnly)), '.');
    }

    public function getHostSubdomainOnly(): string
    {
        $serverName = $this->getFromServerGlobal('SERVER_NAME');

        return rtrim(preg_replace(self::DOMAIN_PATTERN, '', $serverName), '.');
    }

    public function redirect(string $location): void
    {
        header('Location: ' . $location);
        exit;
    }

    public function isCliMode(): bool
    {
        return (php_sapi_name() === 'cli');
    }

    private function getEnvironmentClassPath(): string
    {
        return __FILE__;
    }

    private function getFromServerGlobal(string $key): string
    {
        return $_SERVER[$key] ?? '';
    }

    private function getTidyPath(string $path): string
    {
        return trim(preg_replace('~//+~', '/', $path), '/');
    }
}
