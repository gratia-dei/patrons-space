<?php

class DateMyTopPatronsContentBlock extends ContentBlock implements ContentBlockInterface
{
    public function getContent(string $path, string $fileNameTranslated): string
    {
        $dateWithoutYear = substr($path, 5);

        $contentBlockContent = $this->getOriginalHtmlFileContent('content-blocks/date-my-top-patrons-content-block.html');
        $itemContent = $this->getOriginalHtmlFileContent('items/simple-list-item.html');

        $fileData = $this->getOriginalJsonFileContentArray('my-top-patrons' . self::DATA_FILE_EXTENSION);

        $patronsListContent = '';
        foreach ($fileData[$dateWithoutYear] ?? [] as $patron) {
            $variables = [
                'name' => $patron,
            ];
            $patronsListContent .= $this->getReplacedContent($itemContent, $variables);
        }

        $variables = [
            'date' => $path,
            'top-patrons-list' => $patronsListContent,
        ];

        return $this->getReplacedContent($contentBlockContent, $variables);
    }
}
