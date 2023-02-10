<?php

namespace App\Parsing;

use App\Command\Parse\ParseError;
use App\Csv\CsvFile;
use App\Csv\ScvOpenError;
use App\Tree\Item;
use App\Tree\ItemsList;
use App\Tree\RelatedItem;

class Parser
{
    private string $inputFileName;

    public function __construct(string $inputFileName)
    {
        $this->inputFileName = $inputFileName;
    }

    /**
     * @return string
     * @throws \App\Command\Parse\ParseError
     */
    public function run(): string
    {
        $inputFileName = $this->inputFileName;
        $file = new CsvFile($inputFileName);
        try {
            $stream = $file->stream();
        } catch (ScvOpenError $scvOpenError) {
            throw new ParseError($scvOpenError->getMessage(), $scvOpenError->getCode(), $scvOpenError);
        }

        $result = $this->parse($stream, $inputFileName);

        try {
            $resultString = \json_encode($result, \JSON_THROW_ON_ERROR | \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
        } catch (\JsonException $jsonException) {
            throw new ParseError($jsonException->getMessage(), $jsonException->getCode(), $jsonException);
        }

        return $resultString;
    }

    /**
     * @param resource $stream
     * @param string   $fileName
     *
     * @return \App\Tree\ItemsList
     * @throws \App\Command\Parse\ParseError
     */
    private function parse($stream, string $fileName): ItemsList
    {
        if (false === \fgetcsv($stream, null, ';')) {
            throw new ParseError("Empty File: {$fileName}");
        }
        $rootItems = new ItemsList();
        /** @var array<string, Item> $items */
        $items = [];
        /** @var array<string,array<string, Item>> $childrenMap */
        $childrenMap = [];
        /** @var array<string,array<string, Item>> $relativeMap */
        $relativeMap = [];
        /** @var array $line */
        while (false !== ($line = \fgetcsv($stream, null, ';'))) {
            $name = $line[0];
            $type = $line[1] ?: null;
            $parentName = $line[2] ?: null;
            $relation = $line[3] ?: null;
            $item = new Item($name);
            $items[$item->name()] = $item;
            if (null === $parentName) {
                $rootItems->addItem($item);
            } else {
                $loadedParent = $items[$parentName] ?? null;
                if (null === $loadedParent) {
                    $childrenMap[$parentName][$item->name()] = $item;
                } else {
                    $loadedParent->addChild($item);
                    $item->setParent($loadedParent);
                }
            }
            if (null !== $relation) {
                $relativeMap[$relation][$item->name()] = $item;
            }
        }

        $this->processChildren($childrenMap, $items);
        $this->processRelatives($relativeMap, $items);

        return $rootItems;
    }

    /**
     * @param array<string,array<string, Item>> $childrenMap
     * @param array<string, Item>               $items
     *
     * @return void
     */
    private function processChildren(array $childrenMap, array $items): void
    {
        foreach ($childrenMap as $parentName => $children) {
            foreach ($children as $child) {
                $child->setParent($items[$parentName] ?? null);
            }
        }
    }

    /**
     * @param array<string,array<string, Item>> $relativeMap
     * @param array<string, Item>               $items
     *
     * @return void
     */
    private function processRelatives(array $relativeMap, array $items): void
    {
        foreach ($relativeMap as $relativeName => $relatives) {
            $related = $items[$relativeName];
            foreach ($relatives as $relative) {
                foreach ($related->children() as $child) {
                    $relative->addChild(new RelatedItem($child, $relative));
                }
            }
        }
    }
}