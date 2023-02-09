<?php

namespace App\Tree;

interface ItemInterface
{
    public function name(): string;

    /**
     * @return \App\Tree\ItemInterface[]
     */
    public function children(): array;

}