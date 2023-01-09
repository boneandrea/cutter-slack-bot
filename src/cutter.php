<?php

namespace App;

require_once("vendor/autoload.php");

use App\CutterBot;

ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
ini_set('xdebug.var_display_max_depth', -1);

require_once("src/util.php");

try {
    $json=json_decode(file_get_contents("php://input") ?? "{}", true);
    $x=new CutterBot();
    $x->handleMessage($json);
} catch(Exception $e) {
    l($e->getMessage());
}
exit;
