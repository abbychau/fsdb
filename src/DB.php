<?php

namespace abbychau\mydb;

class DB
{
  private $FS;
  //constructor
  public function __construct($dir)
  {
    $this->FS = new Filesystem($dir);
  }
}

// a class implement arrayaccess
class FileSystem implements \ArrayAccess
{
  private $dir = "";

  public function __construct($dir)
  {
    $this->dir = getcwd() . DIRECTORY_SEPARATOR . $dir;
  }

  private function createDir($dir)
  {
    if (!file_exists($dir)) {
      mkdir($dir, 0777, true);
    }
  }

  private function createFile($file, $content)
  {
    $this->createDir(dirname($file));
    file_put_contents($file, $content);
  }

  public function offsetSet($offset, $value): void
  {
    if (is_array($offset)) {
      $this->createDir($offset);
    } else {
      $this->createFile($offset, $value);
    }
  }

  public function offsetExists($offset): bool
  {
    return file_exists($this->dir . DIRECTORY_SEPARATOR . $offset);
  }

  public function offsetUnset($offset): void
  {
    unlink($this->dir . DIRECTORY_SEPARATOR . $offset);
  }

  public function offsetGet($offset)
  {
    return file_get_contents($this->dir . DIRECTORY_SEPARATOR . $offset);
  }
}
