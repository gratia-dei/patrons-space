<?php

class RemoveAllGeneratedFilesProcedure extends Procedure
{
    public function run(): void
    {
        $generatedFilesSuffix = self::GENERATED_FILE_NAME_SUFFIX . self::DATA_FILE_EXTENSION;
        $dataPath = $this->getPath()->getDataPath();

        $paths = $this->getPathTree($dataPath);
        foreach ($paths as $path => $isDirectory) {
            if (!$isDirectory && mb_substr($path, -mb_strlen($generatedFilesSuffix)) === $generatedFilesSuffix) {
                $isRemoved = $this->getFile()->removeFile($path);
                if (!$isRemoved) {
                    $this->print("generated file '$path' remove error");
                }
            }
        }
    }
}
