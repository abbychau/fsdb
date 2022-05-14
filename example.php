<?php

namespace abbychau\fsdb;

include("src/FileSystem.php");

$db = new FileSystem("test");
$db->reset();
//exit;
$db['1'] = "1";
$db['2'] = ['testing' => '3'];
$db['2'] = [3, 2, 1, 4];
$db['3'] = ["asdf"];
$db[4] = [];
$db[4]['s'] = "asdf";

$db[]="cccc";
$db[]="cccc2";

//echo json_encode($db);

foreach ($db as $k => $v) {
  echo $k."\n";
  var_dump($v);
}

echo "2-3: {$db['2'][3]}"."\n";
echo json_encode($db[4]);

$db->reset();