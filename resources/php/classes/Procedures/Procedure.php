<?php

class Procedure extends Base
{
    private const ERROR_MESSAGE_PREFIX = 'ERROR! ';

    protected function print($data): void
    {
        echo $this->getDate()->getCurrentDateTime() . ' [' . get_called_class() . '] ' . print_r($data, true) . "\n";
    }

    protected function error(string $message): void
    {
        $this->print(self::ERROR_MESSAGE_PREFIX . $message);

        throw new GeneratorException($message);
    }

    protected function getPathTree(string $path): array
    {
        $result = [rtrim($path, '/') => $this->getFile()->isDirectory($path)];

        $elements = $this->getFile()->getList($path);
        foreach ($elements as $elementPath) {
            if ($this->getFile()->isDirectory($elementPath)) {
                $result = array_merge($result, $this->getPathTree($elementPath));
            } else {
                $result[$elementPath] = false;
            }
        }

        return $result;
    }

    protected function setJsonFileContentFromArray(string $fullFilePath, array $data): bool
    {
        $content = $this->getJson()->encode($data);
        $bytesSaved = $this->getFile()->setFileContent($fullFilePath, $content);

        return ($bytesSaved > 0);
    }
}
