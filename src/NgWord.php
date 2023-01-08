<?php

require_once("Iperform.php");

class NgWord implements Iperform
{
    public const FILENAME="ng_words.txt";
    private $words=[];
    private $count;

    public function test(string $text)
    {
        $ngwords=$this->read_ngwords();
        $matched=array_filter($ngwords, fn ($w) =>preg_match("/".$w."/s", $text));

        return ($this->count=count($matched)) > 0;
    }

    public function perform($slack, string $thread_ts)
    {
        $kuwata=$this->read_kuwata();

        return $slack->send(
            "#general",
            $thread_ts,
            $this->count.
            "個のNGワードがあったのや。".
            $kuwata[array_rand($kuwata, 1)]
        );
    }

    public function read_ngwords()
    {
        $words=file_get_contents(self::FILENAME);
        $words=explode("\n", $words);
        $words=array_filter($words);
        return array_map(fn ($e) =>trim($e), $words);
    }
    public function read_kuwata(): array
    {
        $kuwata=file_get_contents("kuwata.txt");
        $kuwata=explode("\n", $kuwata);
        $kuwata=array_filter($kuwata);
        return array_map(fn ($e) =>trim($e), $kuwata);
    }
}
