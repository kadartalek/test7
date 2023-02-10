<?php

namespace App\Tree;

class RelatedItem implements \JsonSerializable, ItemInterface
{
    private ?string $parentName = null;
    private ItemInterface $item;

    public function __construct(ItemInterface $item, Item $parent)
    {
        $this->item = $item;
        $this->setParent($parent);
    }

    public function jsonSerialize(): array
    {
        return [
            'itemName' => $this->name(),
            'parent'   => $this->parentName(),
            'children' => \array_values($this->children()),
        ];
    }

    /**
     * @param \App\Tree\Item $parent
     *
     * @return $this
     */
    public function setParent(Item $parent): static
    {
        /// Now we need only Parent Name, so registering only name,
        /// to avoid unnecessary circular references.
        /// In the future we may need to register parent reference,
        /// so using this method, instead direct name setter
        $this->parentName = $parent->name();
        return $this;
    }

    /**
     * @return \App\Tree\ItemInterface
     */
    public function item(): ItemInterface
    {
        return $this->item;
    }

    public function name(): string
    {
        return $this->item()->name();
    }

    public function children(): array
    {
        return $this->item()->children();
    }

    private function parentName(): ?string
    {
        return $this->parentName;
    }
}