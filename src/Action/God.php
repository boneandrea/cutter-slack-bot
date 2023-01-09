<?php

declare(strict_types=1);

namespace App\Action;

class God implements IPerform
{
    public function test(string $text)
    {
        return preg_match("/神/s", $text);
    }

    public function perform($slack, string $thread_ts)
    {
        return $slack->send_image(
            message: "そうでしゅねぇ〜",
            channels: "#general",
            thread_ts: $thread_ts,
            alt_text: "唯一神.jpg",
            image:"god.jpg"
        );
    }
}
