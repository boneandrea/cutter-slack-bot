<?php

declare(strict_types=1);

namespace App;

define("TOKEN_FILE", "access_token.json");
define("CLIENT_ID", "405980369974.4586316536706");
define("CLIENT_SECRET", "d59a0cf9e44f88c2797056656997d8d5");
define("API_ROOT", "https://slack.com/api");

class Slack
{
    private $token;
    public function __construct()
    {
        $this->token=json_decode(file_get_contents(TOKEN_FILE), true);
    }

    public function send($channel, $thread_ts, $message="オッスmunou")
    {
        $data = [
            "token" => $this->token["access_token"],
            "channel" => $channel,
            "text" => $message,
            "username" => "MySlackBot",
            "thread_ts" => $thread_ts,
        ];
        $r=$this->http_post("/chat.postMessage", $data);
        $result=json_decode($r, true);
        //l($result);
        return $result;
    }

    public function send_image($message="オッスmunou", $channels, $thread_ts, $alt_text, $image)
    {
        if (!$image) {
            return [];
        }
        $cfile = new \CURLFile(dirname(__FILE__)."/../image/".$image, 'image/jpeg', $alt_text);
        $data = [
            "token" => $this->token["access_token"],
            "channels" => $channels, // comma separated
            "text" => $message,
            "username" => "MySlackBot",
            "thread_ts" => $thread_ts,
            'initial_comment'=>$message,
            "file"=>$cfile,
        ];

        $r=$this->http_post("/files.upload", $data, image: true);
        $result=json_decode($r, true);
        //l($result);
        return $result;
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
