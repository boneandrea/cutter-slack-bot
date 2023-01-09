<?php

declare(strict_types=1);

namespace App\Action;

class NgWord implements Iperform
{
    public const NGWORD_FILENAME="ng_words.txt";
    public const KUWATA_FILENAME="kuwata.txt";
    private $words=[];
    private $matched;

    public function test(string $text)
    {
        $ngwords=$this->read_ngwords();
        $this->matched=array_filter($ngwords, fn ($w) =>preg_match("/".$w."/s", $text));

        return count($this->matched) > 0;
    }

    public function perform($slack, string $thread_ts)
    {
        $kuwata=$this->read_kuwata();

        return $slack->send(
            "#general",
            $thread_ts,
            count($this->matched).
            "個のNGワードがあったのや。".
            $kuwata[array_rand($kuwata, 1)]
        );
    }

    public function read_ngwords()
    {
        return $this->read_file(self::NGWORD_FILENAME);
    }
    public function read_kuwata(): array
    {
        return $this->read_file(self::KUWATA_FILENAME);
    }

    public function read_file(string $filename): array
    {
        $words=file_get_contents($filename);
        $words=explode("\n", $words);
        $words=array_filter($words);
        return array_map(fn ($e) =>trim($e), $words);
    }
}
