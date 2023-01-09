<?php

namespace App\Action;

class NgWord implements IPerform
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
        $words=file_get_contents(self::NGWORD_FILENAME);
        $words=explode("\n", $words);
        $words=array_filter($words);
        return array_map(fn ($e) =>trim($e), $words);
    }
    public function read_kuwata(): array
    {
        $kuwata=file_get_contents(self::KUWATA_FILENAME);
        $kuwata=explode("\n", $kuwata);
        $kuwata=array_filter($kuwata);
        return array_map(fn ($e) =>trim($e), $kuwata);
    }
}
