<?php

class CategoriesContentBlock extends ContentBlock implements ContentBlockInterface
{
    private const NAME_INDEX = 'name';
    private const FEMALE_EQUIVALENT_NAME_INDEX = 'female-equivalent-name';
    private const DESCRIPTION_INDEX = 'description';
    private const ICON_INDEX = 'icon';

    private const TRANSLATED_INDEXES = [
        self::NAME_INDEX,
        self::FEMALE_EQUIVALENT_NAME_INDEX,
        self::DESCRIPTION_INDEX,
    ];

    private $categoryItemContent;
    private $fileData;
    private $textVariables;

    public function prepare(string $path): ContentBlock
    {
        $categoryItemContent = $this->getOriginalHtmlFileContent('items/category-item.html');

        $filePath = $this->getDataFileSuffix($path);
        $fileData = $this->getOriginalJsonFileContentArray($filePath);

        $translations = $this->getPreparedTranslations($fileData);
        $language = $this->getLanguage();
        $textVariables = $this->getTranslatedVariablesForLangData($language, $translations);

        $this->categoryItemContent = $categoryItemContent;
        $this->fileData = $fileData;
        $this->textVariables = $textVariables;

        return $this;
    }

    public function getFullContent(string $translatedName): string
    {
        $mainContent = $this->getOriginalHtmlFileContent('content-blocks/categories-content-block.html');

        $fileData = $this->fileData;

        $variables = [];
        $variables['categories-title'] = $translatedName;

        $categoryItemsContent = '';
        foreach ($fileData ?? [] as $recordId => $recordData) {
            $categoryItemsContent .= $this->getRecordContent($recordId);
        }
        $variables['category-items'] = $categoryItemsContent;

        return $this->getReplacedContent($mainContent, $variables);
    }

    public function getRecordContent(string $recordId): string
    {
        $categoryItemContent = $this->categoryItemContent;
        $categoryRow = $this->fileData[$recordId] ?? [];
        $textVariables = $this->textVariables;

        $name = self::VARIABLE_NAME_SIGN . $recordId . '-' . self::NAME_INDEX . self::VARIABLE_NAME_SIGN;
        if (isset($categoryRow[self::FEMALE_EQUIVALENT_NAME_INDEX])) {
            $name .= '/' . self::VARIABLE_NAME_SIGN . $recordId . '-' . self::FEMALE_EQUIVALENT_NAME_INDEX . self::VARIABLE_NAME_SIGN;
        }
        $description = self::VARIABLE_NAME_SIGN . $recordId . '-' . self::DESCRIPTION_INDEX . self::VARIABLE_NAME_SIGN;

        $variables = [];
        $variables['record-id'] = $recordId;
        $variables['category-name'] = $name;
        $variables['category-description'] = $description;
        $variables['category-icon-src'] = $categoryRow[self::ICON_INDEX] ?? '';

        $content = $this->getReplacedContent($categoryItemContent, $variables);

        return $this->getReplacedContent($content, $textVariables, true);
    }

    private function getPreparedTranslations(array $data): array
    {
        $result = [];

        foreach ($data as $category => $categoryData) {
            foreach ($categoryData as $key => $values) {
                if (in_array($key, self::TRANSLATED_INDEXES)) {
                    $result["$category-$key"] = $values;
                }
            }
        }

        return $result;
    }
}