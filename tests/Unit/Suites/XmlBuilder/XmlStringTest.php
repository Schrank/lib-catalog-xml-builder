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
}
