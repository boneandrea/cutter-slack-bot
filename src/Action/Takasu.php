<?php

declare(strict_types=1);

namespace App\Action;

class Takasu implements IPerform
{
    public function test(string $text)
    {
        return preg_match("/高須/s", $text);
    }

    public function perform($slack, string $thread_ts)
    {
        return $slack->send(
            "#general",
            $thread_ts,
            "あの人は信用できる、国税も恐れない"
        );
    }
}
