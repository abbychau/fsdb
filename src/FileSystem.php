<?php

namespace abbychau\fsdb;

class FileSystem implements \ArrayAccess, \JsonSerializable, \Iterator
{
  private $dir = "";
  private $position = 0;
  private $dirCache = [];

  public function __get($name)
  {
    return $this->offsetGet($name);
  }
  public function __set($name, $value)
  {
    $this->offsetSet($name, $value);
  }
  public function __isset($name)
  {
    return $this->offsetExists($name);
  }
  public function __unset($name)
  {
    $this->offsetUnset($name);
  }
  public function __serialize(): array
  {
    $data['dir'] = $this->dir;
    return $data;
  }
  public function __unserialize(array $data): void
  {
    $this->__construct($data['dir']);
  }
  public function __toString()
  {
    return json_encode($this->traverseDirectoryToArray($this->dir));
  }
  
  public function current()
  {
    return $this->offsetGet($this->dirCache[$this->position]);
  }
  public function key()
  {
    return $this->position;
  }
  public function next(): void
  {
    ++$this->position;
  }
  public function valid(): bool
  {
    return sizeof($this->dirCache) > $this->position;
  }
  public function rewind(): void
  {
    $this->position = 0;
  }

  public function __construct($dir)
  {
    $this->dir = getcwd() . DIRECTORY_SEPARATOR . $dir;
    if (!is_dir($this->dir)) {
      mkdir($this->dir);
    }
    $this->dirCache = array_values(array_diff(scandir($this->dir), array('.', '..')));
    //print_r($this->dirCache);exit;
  }

  private function createDir($dir)
  {
    if (!file_exists($dir)) {
      mkdir($dir, 0777, true);
    }
  }

  private function createFile($file, $content)
  {
    if (is_dir($file)) {
      $this->removePath($file);
    }
    file_put_contents($file, $content);
  }

  private function setValue($offset, $value, $pathSum="")
  {
    //e.g. $fs['1']="1"; => create a file named 1 and with content "1"
    //e.g. $fs['2']=['testing'=>'3']; => create a directory named 2 and a file named testing and with content "3"

    if ($pathSum=="") {
      $_path = $this->dir . DIRECTORY_SEPARATOR . $offset;
    } else {
      $_path = $pathSum . DIRECTORY_SEPARATOR . $offset;
    }

    if (is_array($value)) {
      $this->createDir($_path);
      foreach ($value as $key => $val) {
        $this->setValue($key, $val, $_path);
      }
    } else {
      $this->createFile($_path, $value);
    }
  }

  private function getValue($offset)
  {
    $path = $this->dir . DIRECTORY_SEPARATOR . $offset;
    if (is_dir($path)) {
      return $this->traverseDirectoryToArray($path);
    } else {
      return file_get_contents($path);
    }
  }

  public function offsetSet($offset, $value): void
  {
    if ($this->offsetExists($offset)) {
      $this->offsetUnset($offset);
    }
    $this->setValue($offset, $value);
  }

  public function offsetExists($offset): bool
  {
    return file_exists($this->dir . DIRECTORY_SEPARATOR . $offset);
  }

  public function offsetUnset($offset): void
  {
    $this->removePath($this->dir . DIRECTORY_SEPARATOR . $offset);
  }

  public function offsetGet($offset)
  {
    return $this->getValue($offset);
  }

  public function jsonSerialize()
  {
    return $this->traverseDirectoryToArray($this->dir);
  }

  private function traverseDirectoryToArray($dir)
  {
    $files = scandir($dir);
    $result = [];
    foreach ($files as $file) {
      if ($file != "." && $file != "..") {
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($path)) {
          $result[$file] = $this->traverseDirectoryToArray($path);
        } else {
          $result[$file] = file_get_contents($path);
        }
      }
    }
    return $result;
  }

  private function removePath($path = "")
  {
    if ($path == "") {
      $path = $this->dir;
    }
    if (!file_exists($path)) {
      echo $path."\n";
      return;
    }
    if (!is_dir($path)) {
      unlink($path);
      return;
    }
    $files = array_diff(scandir($path), array('.', '..'));
    foreach ($files as $file) {
      (is_dir("$path/$file")) ? $this->removePath("$path/$file") : unlink("$path/$file");
    }
  }

  public function reset()
  {
    $this->removePath();
  }
}
