<?php
declare(strict_types=1);

namespace LizardsAndPumpkins\MagentoConnector\XmlBuilder;

use PHPUnit\Framework\TestCase;

/**
 * @covers \LizardsAndPumpkins\MagentoConnector\XmlBuilder\XmlString
 */
class XmlStringTest extends TestCase
{
    public function testReturnsSameDocument()
    {
        $xml = "<?xml version=\"1.0\"?>\n<xml/>";
        $container = new XmlString($xml);
        $this->assertSame('<xml/>', $container->getXml());
        $this->assertNotContains('<?xml', $container->getXml());
    }

    public function testRemovesControlCharacters()
    {
        $charactersToTest = array_merge(range(0x00, 0x09), [0x0B, 0x0C], range(0x0E, 0x1F), [0x7F]);

        $xml = '<?xml version="1.0"?><xml>';
        foreach ($charactersToTest as $c) {
            $xml .= chr($c);
        }
        $xml .= '</xml>';
        $container = new XmlString($xml);
        $result = $container->getXml();
        foreach ($charactersToTest as $c) {
            $this->assertNotContains(chr($c), $result);
        }
    }

    public function testNewLineIsNotRemoved()
    {
        $xml = "<?xml version=\"1.0\"?><xml>\n</xml>";
        $container = new XmlString($xml);
        $result = $container->getXml();
        $this->assertContains("\n", $result);
    }

    public function testCarriageReturnIsRemovedByDomDocument()
    {
        $xml = "<?xml version=\"1.0\"?><xml>\r</xml>";
        $container = new XmlString($xml);
        $result = $container->getXml();
        $this->assertNotContains("\r", $result);
    }
}
