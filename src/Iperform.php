<?php

namespace App;

interface Iperform
{
    public function test(string $text);
    public function perform($slack, string $text);
}
