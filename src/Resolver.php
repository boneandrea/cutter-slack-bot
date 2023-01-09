<?php

namespace App;

class Resolver
{
    private $action=[];
    private $text;
    public function __construct(string $text)
    {
        $this->text=$text;
    }
    public function add(Iperform $perform)
    {
        $this->action[]=$perform;
    }

    public function resolve()
    {
        foreach ($this->action as $action) {
            if ($action->test($this->text)) {
                return $action;
            }
        }
        return null;
    }
}
