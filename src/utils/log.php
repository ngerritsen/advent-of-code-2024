<?php

function println(...$msgs): void
{
    foreach ($msgs as $i => $msg) {
        if ($i > 0) echo " ";
        print_r($msg);
    }
    echo PHP_EOL;
}
