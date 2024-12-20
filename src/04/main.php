<?php

require __DIR__ . "/../utils/all.php";

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data): void
{
    $grid = parse_grid($data);

    list($xmas_count, $x_mas_count) = get_xmas_counts($grid);

    print_ln($xmas_count);
    print_ln($x_mas_count);
}

function get_xmas_counts(array $grid): array
{
    $total_xmas = 0;
    $total_x_mas = 0;

    for ($y = 0; $y < count($grid); $y++) {
        for ($x = 0; $x < count($grid[$y]); $x++) {
            $total_xmas += find_xmas($grid, [$x, $y]);
            if (find_x_mas($grid, [$x, $y])) $total_x_mas++;
        }
    }

    return [$total_xmas, $total_x_mas];
}

function find_xmas(array $grid, array $start): int
{
    $dirs = [[0, -1], [1, -1], [1, 0], [1, 1], [0, 1], [-1, 1], [-1, 0], [-1, -1]];
    return count(array_filter($dirs, fn($d) => find_xmas_dir($grid, $start, $d)));
}

function find_x_mas(array $grid, array $start): bool
{
    list($w, $h) = get_size($grid);
    list($x, $y) = $start;

    if ($x === 0 || $x === $w - 1 || $y === 0 || $y === $h - 1 || get_value($grid, $start) !== "A") return false;

    $top = $grid[$y - 1][$x - 1] . $grid[$y - 1][$x + 1];
    $bottom = $grid[$y + 1][$x - 1] . $grid[$y + 1][$x + 1];

    return array_search($top . $bottom, ["MMSS", "SMSM", "MSMS", "SSMM"]) !== false;
}

function find_xmas_dir(array $grid, array $start, array $dir): bool
{
    list($w, $h) = get_size($grid);

    foreach (["X", "M", "A", "S"] as $i => $letter) {
        $next = add_coord($start, mul_coord($dir, $i));
        if (get_value($grid, $next) !== $letter) return false;
    }

    return true;
}

main($data);
