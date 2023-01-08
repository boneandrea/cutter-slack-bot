z<?php

ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
ini_set('xdebug.var_display_max_depth', -1);

define("BOT_SELF_USERID", "U04HY33JT9N");

require_once("cutter_bot.php");

$x=new CutterBot();
$json=json_decode(file_get_contents("php://input"), true);

$x->handleMessage($json);
exit;
