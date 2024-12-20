<?php

function parse_coord(string $str): array
{
    return array_map("intval", explode(",", trim($str)));
}

function add_coord(array $a, array $b): array
{
    return [$a[0] + $b[0], $a[1] + $b[1]];
}

function sub_coord(array $a, array $b): array
{
    return [$a[0] - $b[0], $a[1] - $b[1]];
}

function mul_coord(array $c, int $n): array
{
    if ($n === 1) return $c;
    return [$c[0] * $n, $c[1] * $n];
}

function eq_coord(array $a, array $b): bool
{
    return $a[0] === $b[0] && $a[1] === $b[1];
}

function str_coord(array $coord): string
{
    return implode(",", $coord);
}