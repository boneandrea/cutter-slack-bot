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
        $images=["god.jpg", "god2.jpg"];
        $image=$images[mt_rand(0, count($images)-1)];

        return $slack->send_image(
            message: "そうでしゅねぇ〜",
            channels: "#general",
            thread_ts: $thread_ts,
            alt_text: "唯一神.jpg",
            image:$image
        );
    }
}
