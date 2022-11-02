<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('bootstrap/cache')
    ->exclude('storage')
    ->exclude('vendor')
    ->in(__DIR__)
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config = new PhpCsFixer\Config();

// More info on rules and rule-sets at:
// - https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/doc/rules/index.rst
// - https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/master/doc/ruleSets/index.rst
return $config->setRules([
    // Rule set as used by the PHP-CS-Fixer development team, highly opinionated.
    '@PhpCsFixer' => true,
    // Rule set as used by the PHP-CS-Fixer development team, highly opinionated. This set contains rules that are risky.
    '@PhpCsFixer:risky' => false,
    // Binary operators should be surrounded by space as configured.
    'binary_operator_spaces' => [
        'operators' => [
            '=>' => 'align_single_space_minimal',
        ],
    ],
    // PHP arrays should be declared using the configured syntax.
    'array_syntax' => ['syntax' => 'short'],
    // Ensure there is no code on the same line as the PHP open tag.
    'linebreak_after_opening_tag' => true,
    // Orders the elements of classes/interfaces/traits/enums.
    'ordered_class_elements' => [
        'order' => [
            'use_trait',
            'case',
            'constant_private',
            'constant_protected',
            'constant_public',
            'property_private',
            'property_protected',
            'property_public',
            'construct',
            'destruct',
            'magic',
            'phpunit',
            'method_private',
            'method_protected',
            'method_public',
        ],
        'sort_algorithm' => 'none',
    ],
    // Ordering use statements.
    'ordered_imports' => true,
    // Write conditions in Yoda style (true), non-Yoda style (['equal' => false, 'identical' => false, 'less_and_greater' => false]) or ignore those conditions (null) based on configuration.
    'yoda_style' => false,
    // Convert double quotes to single quotes for simple strings.
    'single_quote' => true,
    // Removes @param, @return and @var tags that don't provide any useful information.
    'no_superfluous_phpdoc_tags' => false,
    // Pre- or post-increment and decrement operators should be used if possible.
    'increment_style' => ['style' => 'post'],
    // Forbid multi-line whitespace before the closing semicolon or move the semicolon to the new line for chained calls.
    'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
    // Concatenation should be spaced according configuration.
    'concat_space' => ['spacing' => 'one'],
    // An empty line feed must precede any configured statement.
    'blank_line_before_statement' => [
        'statements' => [
            'break',
            'continue',
            'declare',
            'return',
            'throw',
            'try',
            'if',
            'for',
            'foreach',
            'exit',
            'switch',
        ],
    ],
    // @return void and @return null annotations should be omitted from PHPDoc.
    'phpdoc_no_empty_return'  => false,
    'global_namespace_import' => [
        'import_classes'   => true,
        'import_constants' => true,
        'import_functions' => true,
    ],
])
    ->setFinder($finder);
