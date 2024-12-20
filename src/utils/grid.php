<?php

function create_grid(int $width, int $height, string $fill = "."): array
{
    $line = str_repeat($fill, $width) . "\n";

    return parse_grid(str_repeat($line, $height));
}

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

function str_grid(array $grid): string
{
    return implode("\n", array_map(fn ($row) => implode("", $row), $grid));
}

function get_value(array $grid, array $coord): int | string | null
{
    if (!in_bounds($grid, $coord)) return null;
    return $grid[$coord[1]][$coord[0]];
}

function set_value(array &$grid, array $coord, int | string $val): void
{
    if (!in_bounds($grid, $coord)) return;
    $grid[$coord[1]][$coord[0]] = $val;
}