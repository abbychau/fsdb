<?php

namespace abbychau\mydb;

include("src/DB.php");

$db = new FileSystem("test");
$db->reset();
//exit;
$db['1'] = "1";
$db['2'] = ['testing' => '3'];
$db['2'] = [3, 2, 1, 4];
$db['3'] = ["asdf"];
echo json_encode($db);

foreach ($db as $k => $v) {
  echo $k."\n";
  var_dump($v);
}

echo $db['2'][3];
