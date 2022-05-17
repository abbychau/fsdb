<?php declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
  ->exclude('vendor')
  ->in(__DIR__);

$config = new PhpCsFixer\Config();

return $config
  ->setIndent("  ")
  ->setLineEnding("\n")
  ->setRules([
    'array_syntax' => ['syntax' => 'short'],
    'braces' => [
        'allow_single_line_closure' => true, 
        'position_after_functions_and_oop_constructs' => 'same'],
  ])
  ->setFinder($finder);