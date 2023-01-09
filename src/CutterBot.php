<?php

namespace App;

ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
ini_set('xdebug.var_display_max_depth', -1);

define("BOT_SELF_USERID", "U04HY33JT9N");

require_once("src/util.php");

use App\Resolver;
use App\Slack;
use App\Action\Munou;
use App\Action\Kurosawa;
use App\Action\NgWord;
use App\Action\God;

class CutterBot
{
    private $slack;

    public function __construct()
    {
        $this->slack=new Slack();
    }

    // for verifying endpoint
    public function verify_response(array $json)
    {
        $response=[
            "token"=>$json["token"],
            "challenge"=>$json["challenge"],
            "type"=>"url_verification"
        ];

        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($response);
        exit;
    }

    /**
     * 適切に応答する
     * access_tokenが切れていればrenewする
     *
     */
    public function handleMessage(array $json)
    {
        l($event=$json["event"]);
        $user=$event["user"] ?? "";
        $text=$event["text"] ?? "";
        $thread_ts=$event["thread_ts"] ?? "";

        if ($user === BOT_SELF_USERID) {
            l("bot-self message");
            return;
        }

        $resolver=new Resolver($text);
        $resolver->add(new Kurosawa());
        $resolver->add(new NgWord());
        $resolver->add(new Munou());
        $resolver->add(new God());

        if (($action=$resolver->resolve()) === null) {
            return;
        }

        $r=$action->perform($this->slack, $thread_ts);

        if ($r["ok"] ?? "") {
            return;
        }

        l($r);
        if (($r["error"] ?? "") === "token_expired") {
            l("renew token.");
            $this->slack->renewToken();
            $r=$action->perform($this->slack, $thread_ts);
            l("sent again.");
        }
    }
}
