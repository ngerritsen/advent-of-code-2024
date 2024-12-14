<?php

function parse_grid(string $data, bool $numeric = false): array
{
    return array_map(
        fn($l) => array_map(
            fn($c) => $numeric ? (int)$c : $c,
            str_split(trim($l))
        ),
        explode("\n", trim($data))
    );
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

function str_coord(array $coord): string
{
    return implode(",", $coord);
}

function get_size(array $grid): array
{
    return [count($grid[0] ?? []), count($grid)];
}

function in_bounds(array $grid, array $coord): bool
{
    list($x, $y) = $coord;
    list($w, $h) = get_size($grid);
    return $x >= 0 && $x < $w && $y >= 0 && $y < $h;
}

function str_grid(array $grid) {
    return implode("\n", array_map(fn ($row) => implode("", $row), $grid));
}

function get_coord(array $grid, array $coord)
{
    if (!in_bounds($grid, $coord)) return null;
    return $grid[$coord[1]][$coord[0]];
}

function create_grid(int $width, int $height, string $fill = "."): array
{
    $line = str_repeat($fill, $width) . "\n";

    return parse_grid(str_repeat($line, $height));
}