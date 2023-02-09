<?php

namespace App\Tree;

class ItemsList implements \JsonSerializable
{
    /**
     * @var \App\Tree\Item[]
     */
    private array $items = [];

    public function jsonSerialize(): array
    {
        return \array_values($this->items);
    }

    /**
     * @param \App\Tree\Item $item
     *
     * @return $this
     */
    public function addItem(Item $item): static
    {
        $this->items[$item->name()] = $item;
        return $this;
    }

    /**
     * @param string $name
     *
     * @return \App\Tree\Item|null
     */
    public function getItem(string $name): ?Item
    {
        return $this->items[$name] ?? null;
    }
}