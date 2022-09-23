<?php

$finder = PhpCsFixer\Finder::create();

$config = new PhpCsFixer\Config();

return $config->setRules([
    '@PSR2' => true,
    'no_unused_imports' => true,
    'no_alternative_syntax' => true,
    'array_syntax' => [
        'syntax' => 'short'
    ],
    'concat_space' => [
        'spacing' => 'one'
    ]
])
->setFinder($finder);
