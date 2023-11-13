<?php

return (new \PhpCsFixer\Config())
    ->setFinder(\PhpCsFixer\Finder::create()
        ->exclude([
            'vendor',
            'resources',
            'storage',
            'bootstrap',
            'database',
        ])
        ->in(__DIR__)
    );
