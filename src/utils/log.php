<?php

function print_ln(...$msgs): void
{
    foreach ($msgs as $msg) {
        print_r($msg);
    }
    echo PHP_EOL;
}