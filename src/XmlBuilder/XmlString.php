<?php
declare(strict_types=1);

namespace LizardsAndPumpkins\MagentoConnector\XmlBuilder;

class XmlString
{
    /**
     * @var \SimpleXMLElement
     */
    private $xml;

    public function __construct(string $productXml)
    {
        $this->xml = new \DOMDocument();
        $this->xml->loadXML($this->removeControlCharacters($productXml));
        $this->xml->formatOutput = true;
    }

    public function getXml(): string
    {
        return $this->xml->saveXML($this->xml->documentElement);
    }

    private function removeControlCharacters(string $productXml): string
    {
        return preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $productXml);
    }
}
