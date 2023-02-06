<?php

declare(strict_types=1);

namespace App\Action;

class Muzai implements Iperform
{
    public function test(string $text)
    {
        return preg_match("/不倫|ハプニングバー/s", $text);
    }

    public function perform($slack, string $thread_ts)
    {
        return $slack->send(
            "#general",
            $thread_ts,
            "無罪や"
        );
    }
}
