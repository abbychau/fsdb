<?php
require_once 'vendor/autoload.php';
$parser = new iamcal\SQLParser();

// complex SELECT query with joins
$query = 'SELECT * FROM users u JOIN user_profiles up ON u.id = up.user_id';
$res = $parser->parse($query);
print_r($res);

