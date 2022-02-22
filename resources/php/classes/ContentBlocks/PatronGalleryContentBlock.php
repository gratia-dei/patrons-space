<?php

class PatronGalleryContentBlock extends ContentBlock implements ContentBlockInterface
{
    const IMAGES_KEY_NAME = 'images';

    private $filaData;

    public function prepare(string $path): ContentBlock
    {
        $filePath = $this->getDataFileSuffix($path);
        $this->fileData = $this->getOriginalJsonFileContentArray($filePath);

        return $this;
    }

    public function getFullContent(string $translatedName): string
    {
        $content = $this->getOriginalHtmlFileContent('content-blocks/patron-gallery-content-block.html');

        $itemsContent = '';
        foreach ($this->fileData[self::IMAGES_KEY_NAME] ?? [] as $recordId => $recordData) {
          $itemsContent .= $this->getRecordContent($recordId);
        }

        $variables = [
          'gallery-items' => $itemsContent
        ];

        return $this->getReplacedContent($content, $variables);
    }

    public function getRecordContent(string $recordId): string
    {
        $item = $this->getOriginalHtmlFileContent('items/patron-gallery-item.html');

        $recordData = $this->fileData[self::IMAGES_KEY_NAME][$recordId] ?? [];
        $variables = [
            'file-url' => $recordData['file-url'],
            'page-url' => $recordData['page-url'],
            'attribution' => $recordData['attribution'],
        ];

        return $this->getReplacedContent($item, $variables);
    }
}
