z<?php

ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
ini_set('xdebug.var_display_max_depth', -1);

define("BOT_SELF_USERID", "U04HY33JT9N");

require_once("src/util.php");
require_once("src/slack.php");
require_once("src/NgWord.php");

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

        $r=$this->perform($text, $thread_ts);

        if ($r["ok"]) {
            return;
        }
        l($r);
        if (($r["error"] ?? "") === "token_expired") {
            l("renew token.");
            $this->slack->renewToken();
            $this->perform($text, $thread_ts);
            l("sent again.");
        }
    }

    public function read_kuwata(): array
    {
        $kuwata=file_get_contents("kuwata.txt");
        $kuwata=explode("\n", $kuwata);
        $kuwata=array_filter($kuwata);
        return array_map(fn ($e) =>trim($e), $kuwata);
    }

    public function ngword($text, $thread_ts): array
    {
        $words=(new NgWord())->getWords();
        $matched=array_filter($words, fn ($w) =>preg_match("/".$w."/s", $text));

        if (count($matched) === 0) {
            return [];
        }

        $kuwata=$this->read_kuwata();

        return $this->slack->send(
            "#general",
            $thread_ts,
            count($matched) .
            "個のNGワードがあったのや。".
            $kuwata[array_rand($kuwata, 1)]
        );
    }

    public function perform($text, $thread_ts): array
    {
        $r=$this->ngword($text, $thread_ts);
        if (!empty($r)) {
            return $r;
        }

        if (preg_match("/黒沢/s", $text)) {
            return $this->slack->send_image(
                "黒沢さんは重要",
                "#general",
                $thread_ts,
                alt_text:'黒沢さんのセリフ頭に叩き込め.jpg',
                image:"kurosawasan.jpg"
            );
        }

        if (preg_match("/神/s", $text)) {
            return $this->slack->send_image(
                message: "そうでしゅねぇ〜",
                channels: "#general",
                thread_ts: $thread_ts,
                alt_text: "唯一神.jpg",
                image:"god.jpg"
            );
        }

        if (preg_match("/無能/s", $text)) {
            return $this->slack->send("#general", $thread_ts);
        }
        return ["ok"=>true];
    }
}

$x=new CutterBot();
$json=json_decode(file_get_contents("php://input"), true);

$x->handleMessage($json);
exit;
