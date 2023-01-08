<?php

define("TOKEN_FILE", "../access_token.json");
define("CLIENT_ID", "405980369974.4586316536706");
define("CLIENT_SECRET", "9ee2eefaa26906bb89c44f058b0a2c3c");
define("API_ROOT", "https://slack.com/api");

class Slack
{
    private $token;
    public function __construct()
    {
        $this->token=json_decode(file_get_contents(TOKEN_FILE), true);
    }

    public function slack($message="オッスmunou", $channel, $thread_ts)
    {
        $data = [
            "token" => $this->token["access_token"],
            "channel" => $channel,
            "text" => $message,
            "username" => "MySlackBot",
            "thread_ts" => $thread_ts,
        ];
        l($data);
        return $this->http_post("/chat.postMessage", $data);
    }

    public function slack_image($message="オッスmunou", $channels, $thread_ts)
    {
        $cfile = new CURLFile(dirname(__FILE__)."/../image/kurosawasan.jpg", 'image/jpeg', '黒沢さんのセリフ頭に叩き込め.jpg');
        $data = [
            "token" => $this->token["access_token"],
            "channels" => $channels, // comma separated
            "text" => $message,
            "username" => "MySlackBot",
            "thread_ts" => $thread_ts,
            'initial_comment'=>$message,
            "file"=>$cfile,
        ];

        return $this->http_post("/files.upload", $data, image: true);
    }

    public function renewToken()
    {
        $data = [
            "refresh_token" => $this->token["refresh_token"],
            "client_id"=>CLIENT_ID,
            "client_secret"=>CLIENT_SECRET,
            "grant_type"=>"refresh_token",
        ];

        $r=$this->http_post("/oauth.v2.access", $data);

        // renew stored token
        file_put_contents(TOKEN_FILE, $r);
        $this->token=json_decode(file_get_contents(TOKEN_FILE), true);
    }

    public function http_post($apiUrl, $data, $image=false)
    {
        $ch = curl_init(API_ROOT.$apiUrl);
        if ($image) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
        } else {
            $data = http_build_query($data);
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
