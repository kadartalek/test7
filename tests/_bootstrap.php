<?php

(new class {
    public function run(): void
    {
        $root = \dirname(__DIR__);

        require $root . '/vendor/autoload.php';
    }
})->run();
