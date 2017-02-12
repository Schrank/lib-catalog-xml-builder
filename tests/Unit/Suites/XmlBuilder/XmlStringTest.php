<?php
declare(strict_types=1);

namespace LizardsAndPumpkins\MagentoConnector\XmlBuilder;

use PHPUnit\Framework\TestCase;

/**
 * @covers \LizardsAndPumpkins\MagentoConnector\XmlBuilder\XmlString
 */
class XmlStringTest extends TestCase
{
    const CONTROL_CHARACTERS = [
        0x00 => 'Null',
        0x01 => 'Start of Heading',
        0x02 => 'Start of Text',
        0x03 => 'End of Text',
        0x04 => 'End of Transmission',
        0x05 => 'Enquiry',
        0x06 => 'Acknowledgement',
        0x07 => 'Bell',
        0x08 => 'Backspace[e][f]',
        0x09 => 'Horizontal Tab[g]',
        0x0B => 'Vertical Tab',
        0x0C => 'Form Feed',
        0x0E => 'Shift Out',
        0x0F => 'Shift In',
        0x10 => 'Data Link Escape',
        0x11 => 'Device Control 1 (often XON)',
        0x12 => 'Device Control 2',
        0x13 => 'Device Control 3 (often XOFF)',
        0x14 => 'Device Control 4',
        0x15 => 'Negative Acknowledgement',
        0x16 => 'Synchronous Idle',
        0x17 => 'End of Transmission Block',
        0x18 => 'Cancel',
        0x19 => 'End of Medium',
        0x1A => 'Substitute',
        0x1B => 'Escape[j]',
        0x1C => 'File Separator',
        0x1D => 'Group Separator',
        0x1E => 'Record Separator',
        0x1F => 'Unit Separator',
        0x7F => 'Delete[l][f]',
    ];

    public function testReturnsSameDocument()
    {
        $xml = "<?xml version=\"1.0\"?>\n<xml/>";
        $container = new XmlString($xml);
        $this->assertSame('<xml/>', $container->getXml());
        $this->assertNotContains('<?xml', $container->getXml());
    }

    public function testRemovesControlCharacters()
    {
        $charactersToTest = array_keys(self::CONTROL_CHARACTERS);

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
