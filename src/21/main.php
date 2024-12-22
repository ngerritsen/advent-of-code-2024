<?php

require __DIR__ . "/../utils/all.php";

define("NUMPAD", "789\n456\n123\n.0A");
define("DIRPAD", ".^A\n<v>");
define("DIRS", [">" => [1, 0], "v" => [0, 1], "<" => [-1, 0], "^" => [0, -1]]);

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data): void
{
    $codes = explode("\n", trim($data));

    println(get_total_complexity($codes, 2));
    println(get_total_complexity($codes, 25));
}


function get_total_complexity(array $codes, int $depth): int
{
    $tot = 0;
    $cache = [];
    $num_paths = get_paths(parse_grid(NUMPAD));
    $dir_paths = get_paths(parse_grid(DIRPAD));

    foreach ($codes as $code) {
        $moves = get_initial_moves($num_paths, $code);
        $min = min(array_map(fn ($m) => get_min_moves($m, $dir_paths, $depth, $cache), $moves));
        $tot += $min * (int)trim($code, "A");
    }

    return $tot;
}

function get_min_moves(string $moves, array $paths, int $depth, array &$cache): int
{
    $count = 0;

    for ($i = 0; $i < strlen($moves) - 1; $i++) {
        $move = $moves[$i] . $moves[$i + 1];
        $possible_next_moves = $paths[$move];

        if ($depth === 1) {
            $count += strlen($possible_next_moves[0]);
            continue;
        }

        if (isset($cache[$depth . $move])) {
            $count += $cache[$depth . $move];
            continue;
        }

        $min_count = PHP_INT_MAX;

        foreach ($possible_next_moves as $next_moves) {
            $min_count = min($min_count, get_min_moves("A" . $next_moves, $paths, $depth - 1, $cache));
        }

        $cache[$depth . $move] = $min_count;
        $count += $min_count;
    }

    return $count;
}

function get_initial_moves(array $paths, string $code, int $min = PHP_INT_MAX): array
{
    $all = [];
    $stack = [["A", $code, "A"]];

    while (!empty($stack)) {
        list($prev, $code, $moves) = array_shift($stack);

        if (strlen($moves) > $min) continue;

        if (empty($code)) {
            $all[] = $moves;
            $min = strlen($moves);
            continue;
        }

        $curr = $code[0];
        $remainder = substr($code, 1);

        foreach ($paths[$prev . $curr] as $path) {
            $stack[] = [$curr, $remainder, $moves . $path];
        }
    }

    return $all;
}

function get_paths(array $pad): array
{
    $paths = [];
    $keys = array_filter(array_merge(...$pad), fn ($k) => $k !== ".");

    foreach ($keys as $a) {
        foreach ($keys as $b) {
            $paths[$a . $b] = get_paths_for($pad, $a, $b);
        }
    }

    return $paths;
}

function get_paths_for(array $pad, string $from, string $goal): array
{
    $paths = [];
    $stack = [[find_value($pad, $from), $from, ""]];

    while(!empty($stack)) {
        list($curr, $path, $moves) = array_shift($stack);

        if (!empty($paths) && strlen($moves) > strlen($paths[0])) break;
        if (get_value($pad, $curr) === $goal) {
            $paths[] = $moves . "A";
            continue;
        }

        foreach (DIRS as $move => $dir) {
            $next = add_coord($curr, $dir);
            $to = get_value($pad, $next, ".");
            if ($to === "." || strpos($path, $to) !== false) continue;
            $stack[] = [$next, $path . $to, $moves . $move];
        }
    }

    return $paths;
}

main($data);