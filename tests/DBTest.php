<?php

use PHPUnit\Framework\TestCase;

class DBTest extends TestCase
{
  public function testFileSystemJsonResult()
  {
    $fs = new abbychau\mydb\FileSystem("test");
    $fs->reset();
    //exit;
    $fs['1']="1";
    $fs['2']=['testing'=>'3'];
    $json = json_encode($fs);

    $obj = json_decode($json, true);
    $this->assertEquals($obj['1'], "1");
    $this->assertEquals($obj['2']['testing'], "3");
  }
}
