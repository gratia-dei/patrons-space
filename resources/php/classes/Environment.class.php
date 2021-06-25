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

        return rtrim($requestPath, '/');
    }

    public function getHttpStatusCode(): string
    {
        return $this->getFromServerGlobal('REDIRECT_STATUS');
    }

    public function getHostProtocol(): string
    {
        return $this->getFromServerGlobal('HTTPS') ? 'https://' : 'http://';
    }

    public function getHostDomain(): string
    {
        $serverName = $this->getFromServerGlobal('SERVER_NAME');
        $subdomainOnly = $this->getHostSubdomainOnly();

        return ltrim(mb_substr($serverName, mb_strlen($subdomainOnly)), '.');
    }

    public function getHostSubdomainOnly(): string
    {
        $serverName = $this->getFromServerGlobal('SERVER_NAME');

        return rtrim(preg_replace(self::DOMAIN_PATTERN, '', $serverName), '.');
    }

    private function getEnvironmentClassPath(): string
    {
        return __FILE__;
    }

    private function getFromServerGlobal(string $key): string
    {
        return $_SERVER[$key] ?? '';
    }
}
