<?php

use PHPUnit\Framework\TestCase;

class DBTest extends TestCase
{
  public function testIsThereAnySyntaxError()
  {
    $var = new abbychau\mydb\DB("test");
    $this->assertTrue(is_object($var));
    unset($var);
  }
}
