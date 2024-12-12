<?php

$data = file_get_contents(__DIR__ . "/input.txt");

define("DIRS", [
    "U" => [0, -1],
    "R" => [1, 0],
    "D" => [0, 1],
    "L" => [-1, 0]
]);

define("VERT_DIRS", ["U", "D"]);

function main(string $data)
{
    $grid = parse_grid($data);

    echo get_cost($grid, false) . PHP_EOL;
    echo get_cost($grid, true) . PHP_EOL;
}

function get_cost(array $grid, bool $bulk): int
{
    $used = [];
    list($w, $h) = get_size($grid);
    $cost = 0;

    for ($y = 0; $y < $h; $y++) {
        for ($x = 0; $x < $w; $x++) {
            if (isset($used[str([$x, $y])])) continue;
            $cost += get_region_cost([$x, $y], $grid, $used, $bulk);
        }
    }

    return $cost;
}

function get_region_cost(array $start, array $grid, array &$used, bool $bulk): int
{
    $stack = [$start];
    $plant = get($start, $grid);
    $used[str($start)] = $plant;
    $area = 0;
    $connections = 0;
    $fences = [];

    while (count($stack) > 0) {
        $curr = array_pop($stack);
        $area++;

        foreach (DIRS as $dir_name => $dir) {
            $next = add_dir($curr, $dir);

            if (!is_in_bounds($next, $grid) || get($next, $grid) !== $plant) {
                if (!$bulk) continue;

                $is_horz = array_search($dir_name, VERT_DIRS) === false;
                $level = $is_horz ? $curr[0] : $curr[1];
                $i = $is_horz ? $curr[1] : $curr[0];

                if (!isset($fences[$dir_name])) $fences[$dir_name] = [];
                if (!isset($fences[$dir_name][$level])) $fences[$dir_name][$level] = [];

                $fences[$dir_name][$level][] = $i;

                continue;
            };

            $connections++;

            if (isset($used[str($next)])) continue;

            $stack[] = $next;
            $used[str($next)] = $plant;
        }
    }

    if (!$bulk) {
        $perimeter = ($area * 4) - $connections;
        return $area * $perimeter;
    }

    $sides = 0;

    foreach ($fences as $levels) {
        foreach ($levels as $level) {
            $chunks = 0;
            $prev = -2;
            sort($level);

            foreach ($level as $i) {
                if ($i > $prev + 1) $chunks++;
                $prev = $i;
            }

            $sides += $chunks;
        }
    }

    return $area * $sides;
}

function parse_grid(string $data): array
{
    return array_map(
        fn($ln) => str_split($ln),
        explode("\n", trim($data))
    );
}

function str(array $coord): string
{
    return implode(",", $coord);
}

function add_dir(array $coord, array $dir): array
{
    return [$coord[0] + $dir[0], $coord[1] + $dir[1]];
}

function is_in_bounds(array $coord, array $grid): bool
{
    list($w, $h) = get_size($grid);
    list($x, $y) = $coord;
    return $x >= 0 && $y >= 0 && $x < $w && $y < $h;
}

function get(array $coord, array $grid)
{
    return $grid[$coord[1]][$coord[0]];
}

function get_size(array $grid): array
{
    return [count($grid[0]), count($grid)];
}

main($data);
