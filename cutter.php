<?php

ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
ini_set('xdebug.var_display_max_depth', -1);

define("TOKEN_FILE", "access_token.json");
define("BOT_SELF_USERID", "U04HY33JT9N");
define("API_ROOT", "https://slack.com/api");

require_once("util.php");
require_once("src/slack.php");
require("NgWord.php");

class CutterBot
{
    private $slack;

    public function __construct()
    {
        $this->slack=new Slack();
    }

    public function send($thread_ts, $message="オッスmunou")
    {
        $r=$this->slack->slack($message, "#general", $thread_ts);
        l($result=json_decode($r, true));
        return $result;
    }

    public function send_image($thread_ts, $message="オッスmunou")
    {
        $r=$this->slack->slack_image($message, "#general", $thread_ts);
        l($result=json_decode($r, true));
        return $result;
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

        $r=$this->perform($text, $thread_ts);

        if ($r["ok"]) {
            return;
        }

        if (($r["error"] ?? "") === "token_expired") {
            l("renew token.");
            $this->slack->renewToken();
            l("send again.");
            $this->perform($text, $thread_ts);
        }
    }

    public function perform($text, $thread_ts): array
    {
        $ng=false;
        $x=new NgWord();
        $words=$x->getWords();

        foreach ($words as $w) {
            if (preg_match("/".$w."/s", $text)) {
                $ng=true;
            }
        }
        if ($ng) {
            return $this->send($thread_ts, "NGワードがありました");
        }

        if (preg_match("/黒沢/s", $text)) {
            return $this->send_image($thread_ts, "黒沢さんは重要");
        }

        if (preg_match("/無能/s", $text)) {
            return $this->send($thread_ts);
        }
        return [];
    }
}

$x=new CutterBot();
$json=json_decode(file_get_contents("php://input"), true);

$x->handleMessage($json);
exit;
