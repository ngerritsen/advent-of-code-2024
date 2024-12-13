<?php

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data)
{
    $machines = parse_machines($data);

    echo get_total_cost($machines) . PHP_EOL;
    echo get_total_cost($machines, 10 ** 13) . PHP_EOL;
}

function get_total_cost(array $machines, int $add = 0)
{
    return array_reduce($machines, fn($tot, $m) => $tot + solve_machine($m, $add), 0);
}

function solve_machine(array $machine, int $add)
{
    list($a, $b, $t) = $machine;
    list($ax, $ay) = $a;
    list($bx, $by) = $b;

    $tx = $t[0] + $add;
    $ty = $t[1] + $add;

    $an = ($tx * $by - $ty * $bx) / ($ax * $by - $ay * $bx);

    if ((int)$an !== $an) return 0;

    $bn = ($tx - $ax * $an) / $bx;

    return $an * 3 + $bn;
}

function parse_machines(string $data): array
{
    return array_map("parse_machine", explode("\n\n", trim($data)));
}

function parse_machine(string $data): array
{
    return array_map(function (string $l) {
        preg_match_all("/(\d+)/", $l, $nums);
        return array_map(fn($c) => (int)($c), $nums[0]);
    }, explode("\n", trim($data)));
}

main($data);
