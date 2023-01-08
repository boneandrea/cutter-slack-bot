<?php

function l($msg)
{
    error_log(print_r($msg, true)."\n");
}

function d($msg)
{
    var_dump($msg);
}
