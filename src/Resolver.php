<?php

declare(strict_types=1);

namespace App;

use App\Action\IPerform;

class Resolver
{
    private $action=[];

    public function add(IPerform $perform)
    {
        $this->action[]=$perform;
    }

    public function resolve(string $text)
    {
        $text= mb_convert_kana($text, "HV");
        foreach ($this->action as $action) {
            if ($action->test($text)) {
                return $action;
            }
        }
        return null;
    }
}
