<?php

namespace App\Action;

class Munou implements IPerform
{
    public function test(string $text)
    {
        return preg_match("/無能/s", $text);
    }

    public function perform($slack, string $thread_ts)
    {
        return $slack->send("#general", $thread_ts);
    }
}
