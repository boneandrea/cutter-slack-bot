<?php

require_once("util.php");

class NgWord
{
    public const FILENAME="ng_words.txt";
    private $words=[];
    public function __construct()
    {
        $this->words=file_get_contents(self::FILENAME);
        $this->words=explode("\n", $this->words);
        $this->words=array_filter($this->words);
        $this->words=array_map(fn ($e) =>trim($e), $this->words);
    }

    public function getWords()
    {
        return $this->words;
    }
}
