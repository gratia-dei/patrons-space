<?php

class PatronContentBlock extends ContentBlock implements ContentBlockInterface
{
    private const NAMES_INDEX = self::PATRON_NAMES_INDEX;
    private const FEASTS_INDEX = self::PATRON_FEASTS_INDEX;

    private const TRANSLATED_INDEXES = [
        self::NAMES_INDEX,
    ];
    private const FEASTS_TRANSLATED_INDEXES = [
        self::NAMES_INDEX,
    ];

    private $dataLinksContentBlock;
    private $patronGalleryContentBlock;
    private $categoriesContentBlock;

    private $path;
    private $feastRecordContent;
    private $textVariables;

    public function __construct()
    {
        $this->dataLinksContentBlock = new DataLinksContentBlock();
        $this->patronGalleryContentBlock = new PatronGalleryContentBlock();
        $this->categoriesContentBlock = new CategoriesContentBlock();

        parent::__construct();
    }

    public function prepare(string $path): ContentBlock
    {
        $feastRecordContent = $this->getOriginalHtmlFileContent('items/patron-feast-record-item.html');

        $this->prapareConsolidatedDataFilesArray($path);

        $translations = $this->getPreparedTranslations($this->getMainFileData());
        $language = $this->getLanguage();
        $textVariables = $this->getTranslatedVariablesForLangData($language, $translations);

        $this->path = $path;
        $this->feastRecordContent = $feastRecordContent;
        $this->textVariables = $textVariables;

        return $this;
    }

    public function getFullContent(string $translatedName): string
    {
        $mainContent = $this->getOriginalHtmlFileContent('content-blocks/patron-content-block.html');
        $mainFileData = $this->getMainFileData();
        $textVariables = $this->textVariables;

        $variables = [];
        $variables['date-of-birth'] = $this->getFormattedDates($mainFileData['born'] ?? self::UNKNOWN_SIGN);
        $variables['date-of-death'] = $this->getFormattedDates($mainFileData['died'] ?? self::UNKNOWN_SIGN);
        $variables['beatification'] = $this->getDateWithType($mainFileData['beatified'] ?? []);
        $variables['canonization'] = $this->getDateWithType($mainFileData['canonized'] ?? []);
        $variables['order'] = empty($mainFileData['order'] ?? []) ? self::NON_EXISTENCE : $mainFileData['order'];
        $variables['categories'] = $this->getCategoriesList($mainFileData['categories'] ?? []);
        $variables['gallery'] = $this->getGalleryContent();

        $dataLinksTableName = self::VARIABLE_NAME_SIGN . self::NAMES_INDEX . self::MODIFIER_SEPARATOR . self::MODIFIER_FIRST_ELEMENT . self::VARIABLE_NAME_SIGN;
        $variables['data-links-content-block'] = $this->getDataLinksContent($dataLinksTableName);

        $feastsItemsContent = '';
        foreach ($mainFileData[self::FEASTS_INDEX] ?? [] as $recordId => $recordData) {
            $feastsItemsContent .= $this->getRecordContent($recordId);
        }
        $variables['feasts-items'] = $feastsItemsContent;

        $mainContent = $this->getReplacedContent($mainContent, $variables);

        return $this->getReplacedContent($mainContent, $textVariables, true);
    }

    public function getRecordContent(string $recordId): string
    {
        $mainFileData = $this->getMainFileData();
        $feastRecordContent = $this->feastRecordContent;
        $feastRow = $mainFileData[self::FEASTS_INDEX][$recordId] ?? [];

        $variables = [];
        $variables['record-id'] = $recordId;
        $variables['record-activeness-class'] = $this->getRecordActivenessClass($recordId);

        $dataLinksTableName = self::VARIABLE_NAME_SIGN . $this->getPreparedTranslationRecordKey(self::NAMES_INDEX, $recordId) . self::MODIFIER_SEPARATOR . self::MODIFIER_FIRST_ELEMENT . self::VARIABLE_NAME_SIGN;
        $variables['data-links-content-block'] = $this->getDataLinksContent($dataLinksTableName, self::PATRON_FEASTS_PATH . $recordId);

        return $this->getReplacedContent($feastRecordContent, $variables);
    }

    private function getPreparedTranslations(array $data): array
    {
        $result = [];

        foreach ($data as $key => $values) {
            if (in_array($key, self::TRANSLATED_INDEXES)) {
                $result[$key] = $values;
            }
        }

        foreach ($data[self::FEASTS_INDEX] ?? [] as $recordId => $recordData) {
            foreach ($recordData as $key => $values) {
                if (in_array($key, self::FEASTS_TRANSLATED_INDEXES)) {
                    $preparedKey = $this->getPreparedTranslationRecordKey($key, $recordId);
                    $result[$preparedKey] = $values;
                }
            }
        }

        return $result;
    }

    private function getDataLinksContent(string $tableName, string $path = ''): string
    {
        return $this
            ->dataLinksContentBlock
            ->setData($this->getMainFileData())
            ->prepare($path)
            ->getFullContent($tableName)
        ;
    }

    private function getDateWithType(array $data): string
    {
        $result = $data['date'] ?? null;
        $type = $data['type'] ?? null;

        if (is_null($result)) {
            $result = self::NON_EXISTENCE;
        } else {
            $result = $this->getFormattedDate($result);
            if (!is_null($type)) {
                $result .= " (#lang-$type-adverb#)";
            }
        }

        return $result;
    }

    private function getPreparedTranslationRecordKey(string $key, string $recordId): string
    {
        return 'record-' . $recordId . '-' . $key;
    }

    private function getGalleryContent(): string
    {
        return $this
            ->patronGalleryContentBlock
            ->prepare($this->path)
            ->getFullContent('')
        ;
    }

    private function getCategoriesList(array $categories): array
    {
        return $this
            ->categoriesContentBlock
            ->prepare()
            ->getListContent($categories)
        ;
    }
}
