<?php

require __DIR__ . "/../utils/all.php";

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data): void
{
    list($keys, $locks) = parse_keys_locks($data);

    println(get_fit_count($keys, $locks));
}

function get_fit_count(array $keys, array $locks): int
{
    $fit = 0;

    foreach ($locks as $lock) {
        foreach ($keys as $key) {
            foreach ($lock as $i => $pin) {
                if ($pin + $key[$i] > 5) continue 2;
            }

            $fit++;
        }
    }

    return $fit;
}

function parse_keys_locks(string $data)
{
    $parts = explode("\n\n", trim($data));
    $keys = [];
    $locks = [];

    foreach ($parts as $part) {
        $rows = array_map("str_split", explode("\n", $part));
        $rows = array_map(null, ...$rows);
        $is_key = $rows[0][0] === ".";
        $values = array_map(fn($r) => array_count_values($r)["#"] - 1, $rows);
        if ($is_key) $keys[] = $values;
        else $locks[] = $values;
    }

    return [$keys, $locks];
}


main($data);
