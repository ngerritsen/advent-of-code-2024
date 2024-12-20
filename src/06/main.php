<?php

require __DIR__ . "/../utils/all.php";

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data): void
{
    $grid = parse_grid($data);
    print_ln(walk($grid));
    print_ln(find_loop_count($grid));
}

function find_loop_count(array $grid)
{
    $count = 0;

    for ($y = 0; $y < count($grid); $y++) {
        for ($x = 0; $x < count($grid[$y]); $x++) {
            if (get_value($grid, [$x, $y]) !== ".") continue;
            $grid[$y][$x] = "#";
            if (walk($grid) === -1) $count++;
            $grid[$y][$x] = ".";
        }
    }

    return $count;
}

function walk(array $grid): int
{
    $curr = find_guard($grid);
    $dir = [0, -1];
    $locations = [str_coord($curr) => true];
    $positions = [str_coord($curr) . str_coord($dir) => true];

    while (true) {
        $next = add_coord($curr, $dir);
        $char = get_value($grid, $next);

        if ($char === "#") {
            $dir = rotate_dir($dir);
            continue;
        }

        if (empty($char)) return count($locations);

        $locations[str_coord($next)] = true;
        $curr = $next;

        $pos = str_coord($next) . "|" . str_coord($dir);
        if (isset($positions[$pos])) return -1;
        $positions[$pos] = true;
    }
}

function find_guard(array $grid): array
{
    foreach ($grid as $y => $line) {
        $x = array_search("^", $line);
        if ($x !== false) return [$x, $y];
    }
}

function rotate_dir(array $dir): array
{
    if ($dir[1] === 0) return [0, $dir[0]];
    else return [$dir[1] * -1, 0];
}

main($data);
