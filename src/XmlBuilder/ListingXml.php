<?php
declare(strict_types=1);

namespace LizardsAndPumpkins\MagentoConnector\XmlBuilder;

use LizardsAndPumpkins\MagentoConnector\XmlBuilder\Exception\InvalidListDataException;

class ListingXml
{
    const CONDITION_AND = 'and';
    const URL_KEY_REPLACE_PATTERN = '#[^a-zA-Z0-9:_\-./]#';

    public function buildXml(array $category): XmlString
    {
        $this->validateData($category);

        $urlPath = $this->normalizeUrl($category['url_path']);
        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);

        $xml->startElement('listing');

        $xml->writeAttribute('url_key', $urlPath);
        $xml->writeAttribute('locale', $category['locale']);
        $xml->writeAttribute('website', $category['website']);

        $xml->startElement('criteria');
        $xml->writeAttribute('type', self::CONDITION_AND);
        $this->writeCategoryCriteriaXml($xml, $urlPath);
        $this->writeStockAvailabilityCriteriaXml($xml);
        $xml->endElement();

        $this->writeCategoryAttributesXml($xml, $category);

        $xml->endElement();
        return new XmlString($xml->flush());
    }

    /**
     * @param \XMLWriter $xml
     */
    private function writeStockAvailabilityCriteriaXml(\XMLWriter $xml)
    {
        $xml->startElement('criteria');
        $xml->writeAttribute('type', 'or');

        $xml->startElement('attribute');
        $xml->writeAttribute('name', 'stock_qty');
        $xml->writeAttribute('is', 'GreaterThan');
        $xml->text('0');
        $xml->endElement();

        $xml->startElement('attribute');
        $xml->writeAttribute('name', 'backorders');
        $xml->writeAttribute('is', 'Equal');
        $xml->text('true');
        $xml->endElement();

        $xml->endElement();
    }

    /**
     * @param \XMLWriter $xml
     * @param string     $urlPath
     */
    private function writeCategoryCriteriaXml(\XMLWriter $xml, $urlPath)
    {
        $xml->startElement('attribute');
        $xml->writeAttribute('name', 'category');
        $xml->writeAttribute('is', 'Equal');
        $xml->text($urlPath);
        $xml->endElement();
    }

    /**
     * @param \XMLWriter $xml
     * @param array      $category
     */
    private function writeCategoryAttributesXml(\XMLWriter $xml, array $category)
    {
        // TODO: Put into configuration
        $attributeNames = ['meta_title', 'description', 'meta_description', 'meta_keywords'];

        $xml->startElement('attributes');

        array_map(function ($attributeName) use ($xml, $category) {
            $xml->startElement('attribute');
            $xml->writeAttribute('name', $attributeName);
            $xml->startCData();
            $xml->text($category[$attributeName] ?? '');
            $xml->endCData();
            $xml->endElement();
        }, $attributeNames);

        $xml->endElement();
    }

    /**
     * @param string $urlPath
     * @return string
     */
    private function normalizeUrl($urlPath)
    {
        return preg_replace(self::URL_KEY_REPLACE_PATTERN, '_', $urlPath);
    }

    private function validateData(array $listData)
    {
        if (!$listData['website']) {
            throw new InvalidListDataException('List data must contain a website.');
        }

        if (!$listData['locale']) {
            throw new InvalidListDataException('List data must contain a locale.');
        }

        if (!$listData['url_path']) {
            throw new InvalidListDataException('List data must contain a url path.');
        }
    }
}
