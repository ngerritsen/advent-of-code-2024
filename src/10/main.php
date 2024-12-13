<?php

require __DIR__ . "/../utils/grid.php";

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data)
{
    $grid = parse_grid($data, true);

    list($distinct, $peaks) = get_hike_stats($grid);
    echo $peaks . PHP_EOL;
    echo $distinct . PHP_EOL;
}

function get_hike_stats(array $grid): array
{
    list($w, $h) = get_size($grid);
    $tot_paths = 0;
    $tot_peaks = 0;

    for ($y = 0; $y < $h; $y++) {
        for ($x = 0; $x < $w; $x++) {
            if ($grid[$y][$x] !== 0) continue;
            list($peaks, $paths) = get_score($x, $y, $grid);
            $tot_peaks += $peaks;
            $tot_paths += $paths;
        }
    }

    return [$tot_paths, $tot_peaks];
}

function get_score(int $x, int $y, array $grid): array
{
    list($w, $h) = get_size($grid);
    $dirs = [[0, -1], [1, 0], [0, 1], [-1, 0]];
    $stack = [[$x, $y]];
    $peaks = [];
    $paths = 0;

    while (count($stack) > 0) {
        $curr = array_pop($stack);
        list($cx, $cy) = $curr;
        $ch = $grid[$cy][$cx];

        foreach ($dirs as $d) {
            $next = add_coord($curr, $d);
            list($nx, $ny) = $next;
            if ($nx < 0 || $ny < 0 || $nx >= $w || $ny >= $h) continue;
            $nh = $grid[$ny][$nx];
            if ($nh !== $ch + 1) continue;
            if ($nh === 9) {
                $peaks[to_str([$nx, $ny])] = true;
                $paths += 1;
            }
            if ($nh < 9) array_push($stack, $next);
        }
    }

    return [count($peaks), $paths];
}

main($data);
