<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/app',
        __DIR__ . '/bin',
        __DIR__ . '/migrations',
    ])
    ->name('*.php');

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,


        // OVERRIDE PSR-12
        'class_definition' => [
            'single_line' => true,
        ],

        'braces_position' => [
            'classes_opening_brace' => 'same_line',
            'functions_opening_brace' => 'same_line',
        ],

        'declare_strict_types' => true,
        'strict_param' => true,

        'array_syntax' => [
            'syntax' => 'short',
        ],

        'single_quote' => true,

        'trailing_comma_in_multiline' => [
            'elements' => [
                'arrays',
                'arguments',
                'parameters',
                'match',
            ],
        ],

        'no_unused_imports' => true,
        'ordered_imports' => true,

        'binary_operator_spaces' => [
            'default' => 'single_space',
        ],

        'concat_space' => [
            'spacing' => 'one',
        ],

        'blank_line_after_opening_tag' => true,

        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
        ],

        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',
                'property' => 'one',
            ],
        ],

        'no_superfluous_phpdoc_tags' => true,
        'no_empty_phpdoc' => true,

        'return_type_declaration' => [
            'space_before' => 'one',
        ],

        'native_function_casing' => true,

        'trim_array_spaces' => true,

        'whitespace_after_comma_in_array' => true,
    ])
    ->setFinder($finder)
    ->setUsingCache(true);