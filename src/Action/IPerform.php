<?php

namespace App\Action;

interface IPerform
{
    public function test(string $text);
    public function perform($slack, string $text);
}
