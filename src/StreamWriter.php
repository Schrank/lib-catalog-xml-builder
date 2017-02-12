<?php
declare(strict_types=1);

namespace LizardsAndPumpkins\MagentoConnector;

use LizardsAndPumpkins\MagentoConnector\Exception\InvalidTargetException;

class StreamWriter
{
    const PROTOCOL_DELIMITER = '://';

    /**
     * @var string
     */
    private $target;

    /**
     * @var resource
     */
    private $stream;

    /**
     * @var string
     */
    private $filename;

    public function __construct(string $target)
    {
        $this->filename = basename($target);
        $this->target = $this->suffixPathWithDirectorySeparator(dirname($target)) . $this->filename;

        $this->validateTarget();

    }

    private function suffixPathWithDirectorySeparator(string $path): string
    {
        return rtrim($path, '/') . '/';
    }

    private function validateTarget()
    {
        $protocol = strtok($this->target, self::PROTOCOL_DELIMITER) . self::PROTOCOL_DELIMITER;
        if (!in_array($protocol, $this->getAllowedProtocols(), true)) {
            $message = sprintf('"%s" is not one of the allowed protocols: "%s"', $protocol,
                implode(', ', $this->getAllowedProtocols()));

            throw new InvalidTargetException($message);
        }
    }

    private function getAllowedProtocols(): array
    {
        return [
            'ssh2.scp://',
            'ssh2.sftp://',
            'file://',
            'php://',
        ];
    }

    public function upload(string $xmlString)
    {
        file_put_contents($this->target, $xmlString);
    }

    public function writePartialXmlString(string $partialString): int
    {
        return fwrite($this->getUploadStream(), $partialString);
    }

    private function getUploadStream(): resource
    {
        if (!$this->stream) {
            $this->stream = fopen($this->target, 'wb');
        }

        return $this->stream;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}
