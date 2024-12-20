<?php

require __DIR__ . "/../utils/all.php";

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data): void
{
    $disk = array_map(fn($c) => (int)$c, str_split($data));

    print_ln(get_fragmented_checksum($disk));
    print_ln(get_defragmented_checksum($disk));
}

function get_defragmented_checksum(array $disk): int
{
    $cs = 0;
    $pos = [];
    $pc = 0;

    foreach ($disk as $s) {
        $pos[] = $pc;
        $pc += $s;
    }

    for ($i = count($disk) - 1; $i >= 0; $i -= 2) {
        $id = $i / 2;
        $s = $disk[$i];
        $t = $i;

        for ($j = 1; $j < $i; $j += 2) {
            $ts = $disk[$j];
            if ($ts < $s) continue;
            $t = $j;
            $disk[$j] = $ts - $s;
            break;
        }

        $p = $pos[$t];
        $pos[$t] += $s;
        $cs += cs_val($p, $s, $id);
    };

    return $cs;
}

function get_fragmented_checksum(array $disk): int
{
    $cs = 0;
    $p = 0;
    $j = count($disk) - 1;

    for ($i = 0; $i < count($disk); $i++) {
        $s = $disk[$i];

        if ($j < $i) break;

        if ($i % 2 === 0) {
            $id = $i / 2;
            $cs += cs_val($p, $s, $id);
            $p += $s;
            continue;
        }

        while ($s > 0) {
            $id = $j / 2;
            $ss = $disk[$j];
            $d = min([$s, $ss]);
            $s -= $d;
            $disk[$j] = $ss - $d;
            $cs += cs_val($p, $d, $id);
            $p += $d;
            if ($disk[$j] === 0) $j -= 2;
            if ($j < $i) break;
        }
    }

    return $cs;
}

function cs_val(int $p, int $s, int $id): int
{
    return (sum_series($p + $s - 1) - sum_series($p - 1)) * $id;
}

function sum_series(int $n): int
{
    return ($n * ($n + 1)) / 2;
}

main($data);
