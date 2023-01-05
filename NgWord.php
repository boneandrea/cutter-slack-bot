<?php

require_once("util.php");

class NgWord
{
    public const FILENAME="ng_words.txt";
    private $word=[];
    public function __construct()
    {
        $word=file_get_contents(self::FILENAME);
        $word=explode("\n", $word);
        $word=array_filter($word);
        $word=array_map(fn ($e) =>trim($e), $word);
    }

    public function getWords()
    {
        return $this->word;
    }
}
