<?php

require __DIR__ . "/../utils/all.php";

define("NUMPAD", "789\n456\n123\n.0A");
define("DIRPAD", ".^A\n<v>");
define("DIRS", [
    ">" => [1, 0],
    "v" => [0, 1],
    "<" => [-1, 0],
    "^" => [0, -1]
]);

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data): void
{
    $codes = explode("\n", trim($data));
    $num_paths = get_paths(parse_grid(NUMPAD));
    $dir_paths = get_paths(parse_grid(DIRPAD));

    println(get_total_complexity($num_paths, $dir_paths, $codes, 2));
}

function get_total_complexity(array $num_paths, array $dir_paths, array $codes, int $robots): int
{
    $tot = 0;

    foreach ($codes as $code) {
        $min_moves = get_min_moves($num_paths, $dir_paths, $code, $robots);
        $tot += $min_moves * trim($code, "A");
    }

    return $tot;
}

function get_min_moves(array $num_paths, array $dir_paths, string $code, int $robots): int
{
    $moves = get_moves($num_paths, $code);

    for ($i = 0; $i < $robots; $i++) {
        $moves = get_all_moves($dir_paths, $moves);
    }

    return strlen($moves[0]);
}

function get_all_moves(array $paths, array $codes): array
{
    $all = [];
    $min = PHP_INT_MAX;

    foreach ($codes as $code) {
        $moves = get_moves($paths, $code, $min);
        array_push($all, ...$moves);
        if (!empty($moves)) $min = min(array_map("strlen", $moves));
    }

    return array_values(array_filter($all, fn ($m) => strlen($m) === $min));
}

function get_moves(array $paths, string $code, int $min = PHP_INT_MAX): array
{
    $all = [];
    $stack = [["A", $code, ""]];

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
            $stack[] = [$curr, $remainder, $moves . $path . "A"];
        }
    }

    return $all;
}

function get_shortest(array $moves): array
{
    $min = min(array_map("strlen", $moves));
    return array_filter($moves, fn ($m) => strlen($m) === $min);
}

function get_paths(array $pad): array
{
    $paths = [];
    $keys = array_filter(array_merge(...$pad), fn ($k) => $k !== ".");

    foreach ($keys as $a) {
        foreach ($keys as $b) {
            $key = $a . $b;
            $paths[$key] = get_paths_for($pad, $a, $b);
        }
    }

    return $paths;
}

function get_paths_for(array $pad, string $from, string $goal): array
{
    $paths = [];
    $min = PHP_INT_MAX;
    $start = find_value($pad, $from);
    $stack = [[$start, $from, ""]];

    while(!empty($stack)) {
        list($curr, $path, $moves) = array_shift($stack);

        if (strlen($moves) > $min) continue;

        $at = get_value($pad, $curr);

        if ($at === $goal) {
            $paths[] = $moves;
            $min = strlen($moves);
            continue;
        }

        foreach (["<", ">", "^", "v"] as $move) {
            $dir = DIRS[$move];
            $next = add_coord($curr, $dir);
            $to = get_value($pad, $next, ".");

            if ($to === "." || strpos($path, $to) !== false) continue;

            $stack[] = [$next, $path . $to, $moves . $move];
        }
    }

    return $paths;
}

main($data);