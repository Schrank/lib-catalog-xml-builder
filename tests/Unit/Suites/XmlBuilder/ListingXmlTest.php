<?php
declare(strict_types=1);

namespace LizardsAndPumpkins\MagentoConnector\XmlBuilder;

use LizardsAndPumpkins\MagentoConnector\XmlBuilder\Exception\InvalidListDataException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LizardsAndPumpkins\MagentoConnector\XmlBuilder\ListingXml
 */
class ListingXmlTest extends TestCase
{
    private $urlPath = 'my-seo-category-name';
    /**
     * @var ListingXml
     */
    private $listingXml;

    /**
     * @param string $string
     * @return string
     */
    private function removeXmlFormatting($string)
    {
        return preg_replace('/>[^<]+</m', '><', $string);
    }

    /**
     * @param string $listingXmlString
     * @return string[]
     */
    private function getListingAttributesAsArray($listingXmlString)
    {
        $listingAttributes = [];
        $listing = new \SimpleXMLElement($listingXmlString);
        foreach ($listing->attributes->attribute as $attribute) {
            $listingAttributes[(string)$attribute['name']] = (string)$attribute;
        }
        return $listingAttributes;
    }

    protected function setUp()
    {
        $this->listingXml = new ListingXml();
    }

    private function getListData(): array
    {
        return [
            'url_path' => $this->urlPath,
            'locale'   => 'de_DE',
            'website'  => 'foo',
        ];
    }

    public function testExceptionIsThrownIfLocaleIsEmpty()
    {
        $this->expectException(InvalidListDataException::class);

        $listData = [
            'url_path' => 'lala',
            'locale'   => '',
            'website'  => 'foo',
        ];
        $this->listingXml->buildXml($listData);
    }


    public function testExceptionIsThrownIfWebsiteIsEmpty()
    {
        $this->expectException(InvalidListDataException::class);

        $listData = [
            'url_path' => 'lala',
            'locale'   => 'lala',
            'website'  => '',
        ];

        $this->listingXml->buildXml($listData);
    }

    public function testThrowsExceptionIfUrlPathIsEmpty()
    {
        $this->expectException(InvalidListDataException::class);

        $listData = [
            'url_path' => '',
            'locale'   => 'lala',
            'website'  => 'foo',
        ];

        $this->listingXml->buildXml($listData);
    }

    public function testXmlStringIsReturned()
    {
        $this->assertInstanceOf(
            XmlString::class,
            $this->listingXml->buildXml($this->getListData())
        );
    }

    public function testListingNodeContainsUrlKeyAttribute()
    {
        $urlKey = 'foo';
        $listData = $this->getListData();
        $listData['url_path'] = $urlKey;

        $result = $this->listingXml->buildXml($listData);

        $this->assertRegExp(sprintf('/<listing [^>]*url_key="%s"/', $urlKey), $result->getXml());
    }

    public function testListingNodeContainsLocaleAttribute()
    {
        $locale = 'foo';
        $result = $this->listingXml->buildXml($this->getListData());

        $this->assertRegExp(sprintf('/<listing [^>]*locale="%"/', $locale), $result->getXml());
    }

    public function testListingNodeContainsWebsiteAttribute()
    {
        $storeCode = 'foo';
        $result = $this->listingXml->buildXml($this->getListData());

        $this->assertRegExp(sprintf('/<listing [^>]*website="%s"/', $storeCode), $result->getXml());
    }

    public function testListingNodeContainsAndCriteriaNode()
    {
        $result = $this->listingXml->buildXml($this->getListData());
        $this->assertContains('<criteria type="and">', $result->getXml());
    }

    public function testListingNodeContainsCategoryCriteria()
    {
        $result = $this->listingXml->buildXml($this->getListData());
        $expectedXml = sprintf('<attribute name="category" is="Equal">%s</attribute>', $this->urlPath);

        $this->assertContains($expectedXml, $result->getXml());
    }

    public function testListingNodeContainsStockAvailabilityCriteria()
    {
        $result = $this->listingXml->buildXml($this->getListData());

        $expectedXml = <<<'HTML'
<criteria type="or">
    <attribute name="stock_qty" is="GreaterThan">0</attribute>
    <attribute name="backorders" is="Equal">true</attribute>
</criteria>
HTML;

        $this->assertContains($this->removeXmlFormatting($expectedXml), $this->removeXmlFormatting($result->getXml()));
    }

    public function testListingNodeContainsAttributesNode()
    {
        $result = $this->listingXml->buildXml($this->getListData());
        $this->assertContains('<attributes>', $result->getXml());
    }

    /**
     * @param string $attributeCode
     * @param string $attributeValue
     * @dataProvider listingAttributesProvider
     */
    public function testAttributesNodeContainsAttributeWithValue($attributeCode, $attributeValue)
    {
        $listData = $this->getListData();
        $listData[$attributeCode] = $attributeValue;

        $result = $this->listingXml->buildXml($listData);
        $attributes = $this->getListingAttributesAsArray($result->getXml());

        $this->assertArrayHasKey($attributeCode, $attributes);
        $this->assertSame($attributeValue, $attributes[$attributeCode]);
    }

    /**
     * @return array[]
     */
    public function listingAttributesProvider()
    {
        return [
            ['meta_title', 'This would only work in a <CDATA> section'],
            ['description', 'Description with <strong>HTML</strong>'],
            ['meta_description', 'this is a meta description'],
            ['meta_keywords', 'meta keywords lap is cool'],
        ];
    }
}
