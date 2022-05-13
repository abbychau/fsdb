<?php

use PHPUnit\Framework\TestCase;

class DBTest extends TestCase
{
  public function testIsThereAnySyntaxError()
  {
    $var = new abbychau\mydb\DB();
    $this->assertTrue(is_object($var));
    unset($var);
  }


  public function testMethod1()
  {
    $var = new abbychau\mydb\DB();
    $this->assertTrue($var->method1("hey") == 'Hello World');
    unset($var);
  }
}
