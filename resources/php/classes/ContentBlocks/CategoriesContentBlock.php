<?php

class CategoriesContentBlock extends ContentBlock implements ContentBlockInterface
{
    private const CATEGORIES_PATH = '/records/categories';

    private const CATEGORY_FEMALE = 'female';

    private const NAME_INDEX = 'name';
    private const FEMALE_EQUIVALENT_NAME_INDEX = 'female-equivalent-name';
    private const DESCRIPTION_INDEX = 'description';

    private const TRANSLATED_INDEXES = [
        self::NAME_INDEX,
        self::FEMALE_EQUIVALENT_NAME_INDEX,
        self::DESCRIPTION_INDEX,
    ];

    private $categoryItemContent;
    private $fileData;
    private $textVariables;

    public function prepare(string $path = self::CATEGORIES_PATH): ContentBlock
    {
        $categoryItemContent = $this->getOriginalHtmlFileContent('items/category-item.html');

        $fileData = $this->getConsolidatedDataFilesArray($path);

        $translations = $this->getPreparedTranslations($fileData[self::MAIN_FILE_DATA_INDEX] ?? []);
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
        foreach ($fileData[self::MAIN_FILE_DATA_INDEX] ?? [] as $recordId => $recordData) {
            $categoryItemsContent .= $this->getRecordContent($recordId);
        }
        $variables['category-items'] = $categoryItemsContent;

        return $this->getReplacedContent($mainContent, $variables);
    }

    public function getRecordContent(string $recordId): string
    {
        $categoryItemContent = $this->categoryItemContent;
        $categoryRow = $this->fileData[self::MAIN_FILE_DATA_INDEX][$recordId] ?? [];
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

        $content = $this->getReplacedContent($categoryItemContent, $variables);

        return $this->getReplacedContent($content, $textVariables, true);
    }

    public function getListContent(array $categories): array
    {
        $result = [];

        $fileData = $this->fileData;
        $textVariables = $this->textVariables;

        $itemContent = $this->getOriginalHtmlFileContent('items/categories-list-item.html');

        foreach ($categories as $category) {
            $categoryRow = $fileData[self::MAIN_FILE_DATA_INDEX][$category] ?? [];

            $name = self::VARIABLE_NAME_SIGN . $category . '-' . self::NAME_INDEX . self::VARIABLE_NAME_SIGN;
            if (in_array(self::CATEGORY_FEMALE, $categories) && isset($categoryRow[self::FEMALE_EQUIVALENT_NAME_INDEX])) {
                $name = self::VARIABLE_NAME_SIGN . $category . '-' . self::FEMALE_EQUIVALENT_NAME_INDEX . self::VARIABLE_NAME_SIGN;
            }

            $variables = [];
            $variables['category-name'] = $name;

            $content = $this->getReplacedContent($itemContent, $variables);

            $result[] = $this->getReplacedContent($content, $textVariables, true);
        }

        return $result;
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
