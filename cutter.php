<?php

declare(strict_types=1);

require_once("vendor/autoload.php");

use App\CutterBot;

ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
ini_set('xdebug.var_display_max_depth', -1);

require_once("src/util.php");

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    exit;
}

function return_challenge($json)
{
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($json);
    exit;
}

try {
    $app_auth=false;
    $json=json_decode(file_get_contents("php://input") ?? "{}", true);

    // 認証時有効にする
    // Event Subscriptions
    if ($app_auth) {
        l($json["challenge"]);
        return_challenge($json);
    }

    $x=new CutterBot();
    $x->handleMessage($json);
} catch(Exception $e) {
    l($e->getMessage());
}
exit;
