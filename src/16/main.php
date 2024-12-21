<?php

require __DIR__ . "/../utils/all.php";

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data): void
{
    $grid = parse_grid($data);
    list($min, $tiles) = find_cheapest_path($grid);
    println($min);
    println($tiles);
}

function find_cheapest_path(array $grid): array
{
    $start = [1, count($grid) - 2];
    $visited = [str_coord($start) => 0];
    $history = $visited;
    $stack = [[$start, [1, 0], 0, $history]];
    $paths = [];

    while (count($stack) > 0) {
        list($curr, $dir, $score, $history) = array_shift($stack);

        foreach ([$dir, rotate_cw($dir), rotate_ccw($dir)] as $i => $d) {
            $cost = $i === 0 ? 1 : 1001;
            $next = add_coord($curr, $d);
            $obst = get_value($grid, $next);
            $prev = $visited[str_coord($next)] ?? 10e9;

            if ($prev < $score || $obst === "#") continue;
            if ($obst === "E") {
                $final_score = $score + $cost;
                $paths[$final_score] = $paths[$final_score] ?? [];
                $paths[$final_score][] = $history;
                continue;
            }
            
            $visited[str_coord($next)] = $score + 1001;
            $next_history = $history;
            $next_history[str_coord($next)] = true;
            $stack[] = [$next, $d, $score + $cost, $next_history];
        }
    }

    $min_score = min(array_keys($paths));

    $best_tiles = [];

    foreach ($paths[$min_score] as $path) {
        $best_tiles = array_merge($path, $best_tiles);
    }

    return [$min_score, count($best_tiles) + 1];
}

function rotate_cw(array $dir): array
{
    if ($dir[1] === 0) return [0, $dir[0]];
    else return [$dir[1] * -1, 0];
}

function rotate_ccw(array $dir): array
{
    if ($dir[0] === 0) return [$dir[1], 0];
    else return [0, $dir[0] * -1];
}

main($data);