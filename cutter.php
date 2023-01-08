<?php

ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
ini_set('xdebug.var_display_max_depth', -1);

define("TOKEN_FILE", "access_token.json");
define("CLIENT_ID", "405980369974.4586316536706");
define("CLIENT_SECRET", "9ee2eefaa26906bb89c44f058b0a2c3c");
define("BOT_SELF_USERID", "U04HY33JT9N");
define("API_ROOT", "https://slack.com/api");

require_once("util.php");
require("NgWord.php");

class CutterBot
{
    private $token;
    public function __construct()
    {
        $this->token=json_decode(file_get_contents(TOKEN_FILE), true);
    }

    public function slack($message, $channel, $token, $thread_ts)
    {
        $data = [
            "token" => $token,
            "channel" => $channel,
            "text" => $message,
            "username" => "MySlackBot",
            "thread_ts" => $thread_ts,
        ];
        l($data);
        return $this->http_post("/chat.postMessage", $data, image:false);
    }

    public function slack_image($message, $channel, $token, $thread_ts)
    {
        $cfile = new CURLFile(dirname(__FILE__)."/image/kurosawasan.jpg", 'image/jpeg', 'api_image.jpg');
        $data = [
            "token" => $token,
            "channels" => $channel,
            "text" => $message,
            "username" => "MySlackBot",
            "thread_ts" => $thread_ts,
            'initial_comment'=>$message,
            "file"=>$cfile,
        ];

        return $this->http_post("/files.upload", $data, image: true);
    }

    public function renewToken(string $token)
    {
        $data = [
            "refresh_token" => $token,
            "client_id"=>CLIENT_ID,
            "client_secret"=>CLIENT_SECRET,
            "grant_type"=>"refresh_token",
        ];

        $r=$this->http_post("/oauth.v2.access", $data);

        // renew token
        file_put_contents(TOKEN_FILE, $r);
        $this->token=json_decode(file_get_contents(TOKEN_FILE), true);
    }

    public function http_post($apiUrl, $data, $image=false)
    {
        $ch = curl_init(API_ROOT.$apiUrl);
        l($image);
        if ($image) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
        } else {
            $data = http_build_query($data);
        }
        l($data);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public function send($thread_ts, $message="オッスmunou")
    {
        $r=$this->slack($message, "#general", $this->token["access_token"], $thread_ts);
        l($result=json_decode($r, true));
        return $result;
    }

    public function send_image($thread_ts, $message="オッスmunou")
    {
        $r=$this->slack_image($message, "#general", $this->token["access_token"], $thread_ts);
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

        $this->perform($text, $thread_ts);
        if (preg_match("/無能/s", $text)) {
            $r=$this->send($thread_ts);

            if ($r["ok"]) {
                return;
            }

            if (($r["error"] ?? "") === "token_expired") {
                l("renew token.");
                $this->renewToken($this->token["refresh_token"]);
                l("send again.");
                $this->send($thread_ts);
            }
        }
    }

    public function perform($text, $thread_ts)
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
            $r=$this->send($thread_ts, "NGワードがありました");
        }

        if (preg_match("/黒沢/s", $text)) {
            $r=$this->send_image($thread_ts, "黒沢さんは重要");
            return;
        }
    }
}

$x=new CutterBot();
$json=json_decode(file_get_contents("php://input"), true);

//l(longLivedToken($token["refresh_token"])); exit;

$x->handleMessage($json);
exit;
