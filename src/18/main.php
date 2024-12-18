<?php

require __DIR__ . "/../utils/grid.php";

$data = file_get_contents(__DIR__ . "/input.txt");

define("MIN_BYTES", 1024);

function main(string $data)
{
    $bytes = parse_coords($data);
    $grid = create_grid(71, 71);

    echo find_shortest_path(drop_bytes($grid, $bytes, MIN_BYTES)) . PHP_EOL;
    echo find_final_byte($grid, $bytes) . PHP_EOL;
}

function find_final_byte(array $grid, array $bytes): string
{
    $left = MIN_BYTES + 1;
    $right = count($bytes) - 1;

    while ($left !== $right - 1) {
        $mid = $left + floor(($right - $left) / 2);
        $next = drop_bytes($grid, $bytes, $mid);
        $valid = find_shortest_path($next) < PHP_INT_MAX;
        if (!$valid) $right = $mid;
        if ($valid) $left = $mid;
    }

    return str_coord($bytes[$left]);
}

function drop_bytes(array $grid, array $bytes, int $n): array
{
    for ($i = 0; $i < $n; $i++) {
        set_value($grid, $bytes[$i], "#");
    }

    return $grid;
}

function find_shortest_path(array $grid): int
{
    $start = [0, 0];
    list($w, $h) = get_size($grid);
    $end = [$w - 1, $h - 1];
    $visited = [str_coord($start) => 0];
    $stack = [[$start, 0]];
    $dirs = [[1, 0], [0, 1], [-1, 0], [0, -1]];

    while (!empty($stack)) {
        list($curr, $cost) = array_shift($stack);

        if (eq_coord($curr, $end)) {
            return $cost;
        }

        foreach ($dirs as $dir) {
            $next = add_coord($curr, $dir);

            if (isset($visited[str_coord($next)])) continue;

            $visited[str_coord($next)] = $cost;

            if (!in_bounds($grid, $next) || get_value($grid, $next) === "#") continue;

            $stack[] = [$next, $cost + 1];
        }
    }

    return PHP_INT_MAX;
}

function parse_coords(string $data): array
{
    return array_map("parse_coord", explode("\n", trim($data)));
}

main($data);
