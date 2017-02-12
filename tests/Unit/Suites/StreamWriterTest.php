<?php
declare(strict_types=1);

namespace LizardsAndPumpkins\MagentoConnector;

use LizardsAndPumpkins\MagentoConnector\Exception\InvalidTargetException;
use PHPUnit\Framework\TestCase;

/**
 * @covers StreamWriter
 */
class StreamWriterTest extends TestCase
{
    /**
     * @dataProvider getAllowedProtocols
     */
    public function testAllowProtocols(string $protocol)
    {
        $target = $protocol . 'some/path/magento.xml';
        $uploader = new StreamWriter($target);
        $this->assertInstanceOf(StreamWriter::class, $uploader);
    }

    /**
     * @dataProvider getDisallowedProtocols
     */
    public function testDisallowedProtocols(string $protocol)
    {
        $this->expectException(InvalidTargetException::class);
        $target = $protocol . 'some/path/magento.xml';

        new StreamWriter($target);
    }

    public function getAllowedProtocols(): array
    {
        return [
            'ssh2.scp' => ['ssh2.scp://'],
            'ssh2.ssh' => ['ssh2.sftp://'],
            'file'     => ['file://'],
        ];
    }

    public function getDisallowedProtocols(): array
    {
        return [
            'http'           => ['http://'],
            'ftp'            => ['ftp://'],
            'zlib'           => ['zlib://'],
            'data'           => ['data://'],
            'glob'           => ['glob://'],
            'phar'           => ['phar://'],
            'ssh2'           => ['ssh2://'],
            'rar'            => ['rar://'],
            'ogg'            => ['ogg://'],
            'expect'         => ['expect://'],
            'compress.zlib'  => ['compress.zlib://'],
            'compress.bzip2' => ['compress.bzip2://'],
            'zip'            => ['zip://'],
            'ssh2.shell'     => ['ssh2.shell://'],
            'ssh2.exec'      => ['ssh2.exec://'],
            'ssh2.tunnel'    => ['ssh2.tunnel://'],
        ];
    }
}

function file_put_contents(string $filename, mixed $data, int $flags = 0, resource $context)
{
    return \file_put_contents($filename, $data, $flags, $context);
}

function fwrite(resource $handle, string $string, int $length)
{
    return \fwrite($handle, $string, $length);
}

function fopen(string $filename, string $mode, bool $use_include_path = false, resource $context)
{
    return \fopen($filename, $mode, $use_include_path, $context);
}

function fclose(resource $handle)
{
    return \fclose($handle);
}
