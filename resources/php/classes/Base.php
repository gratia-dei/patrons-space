<?php

abstract class Base
{
    private $environment;
    private $file;
    private $json;
    private $path;

    public function __construct()
    {
        $this->date = new Date();
        $this->environment = new Environment();
        $this->file = new File();
        $this->json = new Json();
        $this->path = new Path();
    }

    protected function getDate(): Date
    {
        return $this->date;
    }

    protected function getEnvironment(): Environment
    {
        return $this->environment;
    }

    protected function getFile(): File
    {
        return $this->file;
    }

    protected function getJson(): Json
    {
        return $this->json;
    }

    protected function getPath(): Path
    {
        return $this->path;
    }
}
