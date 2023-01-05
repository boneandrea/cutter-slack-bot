<?php

require("util.php");

class NgWord
{
    public const FILENAME="ng_words.txt";
    private $word=[];
    public function __construct()
    {
        $word=file_get_contents(self::FILENAME);
        $word=explode("\n", $word);
        $word=array_filter($word);
    }

    public function getWords()
    {
        return $this->word;
    }
}
