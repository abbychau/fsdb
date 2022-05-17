<?php

namespace abbychau\fsdb;

class FileSystem implements \ArrayAccess, \JsonSerializable, \Iterator, \Countable {
  private $rootDir = "";
  private $dataDir = "";
  private $metaDir = "";
  private $name = "";

  private $dirStack = [];
  
  private $position = 0;
  private $dirCache = [];

  public function __isset($name) {
    return $this->offsetExists($name);
  }
  public function __unset($name) {
    $this->offsetUnset($name);
  }
  public function __serialize(): array {
    $data['dir'] = $this->dataDir;
    return $data;
  }
  public function __unserialize(array $data): void {
    $this->__construct($data['dir']);
  }
  public function __toString() {
    return json_encode($this->traverseDirectoryToArray($this->dataDir));
  }
  public function current() {
    return $this->offsetGet($this->dirCache[$this->position]);
  }
  public function key() {
    return $this->position;
  }
  public function next(): void {
    ++$this->position;
  }
  public function valid(): bool {
    return sizeof($this->dirCache) > $this->position;
  }
  public function rewind(): void {
    $this->position = 0;
  }

  public function __construct($name,$rootDir="") {
    if($name == ""){
      throw new \Exception("Name cannot be empty");
    }
    if($rootDir == ""){
      $rootDir = getcwd();
    }
    $path = $rootDir . DIRECTORY_SEPARATOR . $name;
    
    $dataPath = $path . DIRECTORY_SEPARATOR . "data";
    $metaPath = $path . DIRECTORY_SEPARATOR . "meta";
    if (!is_dir($path)) {
      //echo "\n will make: " . $path . "\n";
      mkdir($path);
    }
    if (!is_dir($dataPath)) {
      mkdir($dataPath);
    }
    if (!is_dir($metaPath)) {
      mkdir($metaPath);
    }
    $this->rootDir = $path;
    $this->dataDir = $dataPath;
    $this->metaDir = $metaPath;
    $this->name = $name;

    $this->dirCache = array_diff(scandir($path), ['.', '..']);
  }

  private function createDir($dir) {
    if (!file_exists($dir)) {
      mkdir($dir, 0777, true);
    }
  }

  private function createFile($file, $content) {
    if (is_dir($file)) {
      $this->removePath($file);
    }
    file_put_contents($file, $content);
  }

  private function setValue($offset, $value, $pathSum="") {
    //e.g. $fs['1']="1"; => create a file named 1 and with content "1"
    //e.g. $fs['2']=['testing'=>'3']; => create a directory named 2 and a file named testing and with content "3"

    if ($pathSum=="") {
      $_path = $this->dataDir . DIRECTORY_SEPARATOR . $offset;
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

  public function offsetSet($offset, $value): void {
    //var_dump($offset);
    //echo "\n";
    if (is_null($offset)) {

      $x = microtime(true);
      while (true) {
        if (!$this->offsetExists($x)) {
          break;
        }
        $x++;
      }
      $this->setValue($x, $value);
      return;
    }
    if ($this->offsetExists($offset)) {
      $this->offsetUnset($offset);
    }
    $this->setValue($offset, $value);
  }

  public function offsetExists($offset): bool {
    return file_exists($this->dataDir . DIRECTORY_SEPARATOR . $offset);
  }

  public function offsetUnset($offset): void {
    $this->removePath($this->dataDir . DIRECTORY_SEPARATOR . $offset);
  }

public function count():int {
  return sizeof($this->dirCache);
}

  public function offsetGet($offset) {
    // if $offset is a directory, return self , else, return the content
    if (is_dir($this->dataDir . DIRECTORY_SEPARATOR . $offset)) {
      return new self($offset, $this->dataDir . DIRECTORY_SEPARATOR);
    } else {
      return file_get_contents($this->dataDir . DIRECTORY_SEPARATOR . $offset);
    }
  }

  public function jsonSerialize() {
    echo $this->dataDir . "\n\n";
    return $this->traverseDirectoryToArray($this->dataDir);
  }

  private function traverseDirectoryToArray($dir) {
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

  private function removePath($path = "") {
    if ($path == "") {
      $path = $this->dataDir;
    }
    if (!file_exists($path)) {
      echo $path."\n";
      return;
    }
    if (!is_dir($path)) {
      unlink($path);
      return;
    }
    $files = array_diff(scandir($path), ['.', '..']);
    foreach ($files as $file) {
      (is_dir("$path/$file")) ? $this->removePath("$path/$file") : unlink("$path/$file");
    }
    
    rmdir($path);
  }

  public function reset() {
    echo "reset\n";

    print_r($this->dirStack);
    $this->removePath($this->rootDir);
    //remove last item from dirStack
    array_pop($this->dirStack);
    print_r($this->dirStack);
    $this->__construct($this->dirStack);
    echo "end of reset\n";
  }
}
