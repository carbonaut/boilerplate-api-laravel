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

return $config->setRules([
    '@PhpCsFixer'       => true,
    '@PhpCsFixer:risky' => false,
    // Align the arrow functions with the logic within
    'binary_operator_spaces' => ['operators' => ['=>' => 'align_single_space_minimal']],
    // Always use the '[]' instead of 'Array()' sintax
    'array_syntax' => ['syntax' => 'short'],
    // No code in the file's first line
    'linebreak_after_opening_tag' => true,
    // Docs annotations should be ordered by @params, then @throws and then @returns
    'phpdoc_order' => true,
    // Orders the elements of classes by private, then proteced and then public
    'ordered_class_elements' => false,
    // Alphabetical ordering use statements.
    'ordered_imports' => true,
    // Keep the typical order in a conditional statement (value === true instead of true === value)
    'yoda_style' => false,
    // Prefer single quotes for simple strings ('' instead of "")
    'single_quote' => true,
    // Don't remove @param and @return tags without any useful information
    'no_superfluous_phpdoc_tags' => false,
    // Post-increment and decrement operators should be used ($i++ instead of ++$i)
    'increment_style'                        => ['style' => 'post'],
    'phpdoc_to_comment'                      => true,
    'single_line_comment_style'              => ['comment_types' => ['asterisk', 'hash']],
    'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
    'concat_space'                           => ['spacing' => 'one'],
    'blank_line_before_statement'            => ['statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try', 'if', 'foreach']],
])
    ->setFinder($finder);
