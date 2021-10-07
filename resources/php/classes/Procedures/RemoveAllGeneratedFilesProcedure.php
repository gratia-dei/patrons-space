<?php

class RemoveAllGeneratedFilesProcedure extends Procedure
{
    public function run(): void
    {
        $generatedFilesSuffix = $this->getGeneratedFileSuffix();
        $dataPath = $this->getPath()->getDataPath();

        $paths = $this->getPathTree($dataPath);
        foreach ($paths as $path => $isDirectory) {
            if (!$isDirectory && mb_substr($path, -mb_strlen($generatedFilesSuffix)) === $generatedFilesSuffix) {
                $isRemoved = $this->getFile()->removeFile($path);
                if (!$isRemoved) {
                    $this->error("generated file '$path' remove error");
                }
            }
        }
    }
}
