<?php

require __DIR__ . "/../utils/all.php";

$data = file_get_contents(__DIR__ . "/input.txt");

define("DIRS", [
    "U" => [0, -1],
    "R" => [1, 0],
    "D" => [0, 1],
    "L" => [-1, 0]
]);

define("VERT_DIRS", ["U", "D"]);

function main(string $data): void
{
    $grid = parse_grid($data);

    println(get_cost($grid, false));
    println(get_cost($grid, true));
}

function get_cost(array $grid, bool $bulk): int
{
    $used = [];
    list($w, $h) = get_size($grid);
    $cost = 0;

    for ($y = 0; $y < $h; $y++) {
        for ($x = 0; $x < $w; $x++) {
            if (isset($used[str_coord([$x, $y])])) continue;
            $cost += get_region_cost([$x, $y], $grid, $used, $bulk);
        }
    }

    return $cost;
}

function get_region_cost(array $start, array $grid, array &$used, bool $bulk): int
{
    $stack = [$start];
    $plant = get_value($grid, $start);
    $used[str_coord($start)] = $plant;
    $area = 0;
    $connections = 0;
    $fences = [];

    while (count($stack) > 0) {
        $curr = array_pop($stack);
        $area++;

        foreach (DIRS as $dir_name => $dir) {
            $next = add_coord($curr, $dir);

            if (get_value($grid, $next) !== $plant) {
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

            if (isset($used[str_coord($next)])) continue;

            $stack[] = $next;
            $used[str_coord($next)] = $plant;
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

main($data);
