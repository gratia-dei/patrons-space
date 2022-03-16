<?php

class GenerateDateDataFileProcedure extends Procedure
{
    public function run(string $srcPath, string $dstFilePath): void
    {
        $fullFilePath = $this->getFullDataPath($dstFilePath);
        $fullFilePathWithExtension = $this->getGeneratedFileSuffix($fullFilePath);
        $fileData = $this->getOriginalJsonFileContentArray($fullFilePathWithExtension);

        $fullSrcPath = $this->getFullDataPath($srcPath);
        $paths = $this->getPathTree($fullSrcPath);
        foreach ($paths as $path) {
            //... todo
        }

        $this->saveGeneratedFiles([$fullFilePathWithExtension => $fileData], true);
    }
}
