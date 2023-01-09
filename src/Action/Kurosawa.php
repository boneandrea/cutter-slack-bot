<?php

namespace App\Action;

class Kurosawa implements IPerform
{
    public function test(string $text)
    {
        return preg_match("/黒沢/s", $text);
    }

    public function perform($slack, string $thread_ts)
    {
        return $slack->send_image(
            "黒沢さんは重要",
            "#general",
            $thread_ts,
            alt_text:'黒沢さんのセリフ頭に叩き込め.jpg',
            image:"kurosawasan.jpg"
        );
    }
}
