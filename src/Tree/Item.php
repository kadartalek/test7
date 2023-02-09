<?php

namespace App\Tree;

class Item implements \JsonSerializable, ItemInterface
{
    private string $name;
    /**
     * @var \App\Tree\ItemInterface[]
     */
    private array $children = [];

    private ?string $parentName = null;

    public function __construct(string $name, ?self $parent = null)
    {
        $this->name = $name;
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
     * @param static|null $parent
     *
     * @return $this
     */
    public function setParent(?self $parent): static
    {
        /// Now we need only Parent Name, so registering only name,
        /// to avoid unnecessary circular references.
        /// In the future we may need to register parent reference,
        /// so using this method, instead direct name setter
        $this->parentName = $parent?->name();
        return $this;
    }

    /**
     * @param static $child
     *
     * @return $this
     */
    public function addChild(ItemInterface $child): static
    {
        $this->children[$child->name()] = $child;
        return $this;
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return \App\Tree\ItemInterface[]
     */
    public function children(): array
    {
        return $this->children;
    }

    private function parentName(): ?string
    {
        return $this->parentName;
    }
}