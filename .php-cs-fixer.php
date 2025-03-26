<?php
$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR2'                               => true,
        'array_syntax'                        => ['syntax' => 'short'],
        'concat_space'                        => ['spacing' => 'one'],
        'new_with_parentheses'                => true,
        'no_blank_lines_after_phpdoc'         => true,
        'no_empty_phpdoc'                     => true,
        'no_empty_comment'                    => true,
        'no_leading_import_slash'             => true,
        'no_trailing_comma_in_singleline'     => true,
        'no_unused_imports'                   => true,
        'ordered_imports'                     => ['imports_order' => null, 'sort_algorithm' => 'alpha'],
        'phpdoc_add_missing_param_annotation' => ['only_untyped' => true],
        'phpdoc_align'                        => true,
        'phpdoc_no_empty_return'              => true,
        'phpdoc_order'                        => true,
        'phpdoc_scalar'                       => true,
        'phpdoc_to_comment'                   => true,
        'psr_autoloading'                     => true,
        'return_type_declaration'             => ['space_before' => 'none'],
        'blank_lines_before_namespace'        => true,
        'single_quote'                        => true,
        'space_after_semicolon'               => true,
        'ternary_operator_spaces'             => true,
        'trailing_comma_in_multiline'         => true,
        'trim_array_spaces'                   => true,
        'whitespace_after_comma_in_array'     => true,
    ])
    ->setFinder($finder);
