<?php

require __DIR__ . "/../utils/all.php";

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data): void
{
    $grid = parse_grid($data);
    $track = get_track($grid);

    println(get_total_cheats($track, 100, 2));
    println(get_total_cheats($track, 100, 20));
}

function get_total_cheats(array $track, int $min_save, int $max_skip): int
{
    return array_sum(array_map(
        fn ($s) => get_cheats($track, parse_coord($s), $min_save, $max_skip),
        array_flip($track)
    ));
}

function get_cheats(array $track, array $start, int $min_save, int $max_skip): int
{
    $start_step = $track[str_coord($start)];
    $found = 0;

    for ($r = 2; $r <= $max_skip; $r++) {
        $curr = add_coord($start, mul_coord([0,-1], $r));

        foreach ([[1,1], [-1,1], [-1,-1], [1, -1]] as $dir) {
            for ($i = 0; $i < $r; $i++) {
                $curr = add_coord($curr, $dir);
                $step = $track[str_coord($curr)] ?? -1;
                $save = $step - $start_step - $r;
                if ($save >= $min_save) $found ++;
            }
        }
    }

    return $found;
}

function get_track(array $grid): array
{
    $start = find($grid, "S");
    $stack = [[$start]];
    $visited = [];

    while (!empty($stack)) {
        $path = array_shift($stack);
        $curr = $path[count($path) - 1];

        if (get_value($grid, $curr) === "E") break;

        foreach ([[1, 0], [0, 1], [-1, 0], [0, -1]] as $dir) {
            $next = add_coord($curr, $dir);

            if (
                array_key_exists(str_coord($next), $visited) ||
                get_value($grid, $next) === "#"
            ) continue;

            $visited[str_coord($next)] = true;
            $stack[] = array_merge($path, [$next]);
        }
    }

    return array_flip(array_map("str_coord", $path));
}

function find(array $grid, string $char): array
{
    foreach ($grid as $y => $row) {
        foreach ($row as $x => $cell) {
            if ($cell === $char) return [$x, $y];
        }
    }
}

main($data);