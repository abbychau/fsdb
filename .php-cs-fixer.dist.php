<?php declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
  ->exclude('vendor')
  ->in(__DIR__);

$config = new PhpCsFixer\Config();

return $config
  ->setIndent("  ")
  ->setLineEnding("\n")
  ->setFinder($finder);