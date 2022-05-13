<?php
use PHPUnit\Framework\TestCase;

class FileSystemTest extends TestCase
{
  public function testIsThereAnySyntaxError()
  {
    $var = new abbychau\mydb\FileSystem("test");
    $this->assertTrue(is_object($var));
    unset($var);
  }
}
