<?php

namespace App\Csv;

class CsvFile
{
    private string $fileName;
    /**
     * @var resource|null
     */
    private $stream;

    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    public function __destruct()
    {
        if (null !== $this->stream) {
            \fclose($this->stream);
        }
    }

    /**
     * @return resource
     *
     * @throws \App\Csv\ScvOpenError
     */
    public function stream()
    {
        return $this->stream ?? ($this->stream = $this->openStream());
    }

    /**
     * @return resource
     *
     * @throws \App\Csv\ScvOpenError
     */
    private function openStream()
    {
        $fileName = $this->fileName;
        if (!\is_file($fileName)) {
            throw new ScvOpenError("Not is File: {$fileName}");
        }
        if (!\is_readable($fileName)) {
            throw new ScvOpenError("Is not readable: {$fileName}");
        }
        try {
            // Still can be race condition
            $result = \fopen($fileName, 'rb');
        } catch (\Throwable $throwable) {
            throw new ScvOpenError("Error opening file {$fileName}: {$throwable->getMessage()}", 0, $throwable);
        }

        if (false === $result) {
            throw new ScvOpenError("Cannot open file {$fileName}");
        }

        return $result;
    }
}