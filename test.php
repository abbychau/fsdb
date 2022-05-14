<?php

namespace abbychau\mydb;

include("src/DB.php");

$fs = new FileSystem("test");
$fs->reset();
//exit;
$fs['1'] = "1";
$fs['2'] = ['testing' => '3'];
$fs['2'] = [3, 2, 1, 4];
$fs['3'] = ["asdf"];
//echo json_encode($fs);

foreach ($fs as $k => $v) {
  //    echo $k."\n";
    //var_dump($v);
}

echo $fs['2'][3];
