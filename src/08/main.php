<?php

require __DIR__ . "/../utils/grid.php";

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data)
{
    $grid = parse_grid($data);
    $nodes = get_nodes($grid);

    echo get_antinode_count($grid, $nodes, false) . PHP_EOL;
    echo get_antinode_count($grid, $nodes, true) . PHP_EOL;
}

function get_antinode_count(array $grid, array $nodes, bool $unlimited): int
{
    $antinodes = [];

    foreach ($nodes as $locations) {
        $pairs = get_pairs($locations);

        foreach ($pairs as $pair) {
            list($a, $b) = $pair;

            foreach (get_antinodes($a, $b, $grid, $unlimited) as $node) {
                $antinodes[str_coord($node)] = true;
            }

            foreach (get_antinodes($b, $a, $grid, $unlimited) as $node) {
                $antinodes[str_coord($node)] = true;
            }
        }
    }

    return count($antinodes);
}

function get_antinodes(array $a, array $b, array $grid, bool $unlimited): array
{
    $antinodes = [];
    $step = sub_coord($a, $b);

    if ($unlimited) $antinodes[] = $a;

    while (true) {
        $antinode = add_coord($a, $step);
        if (!in_bounds($grid, $antinode)) break;
        $antinodes[] = $antinode;
        if (!$unlimited) break;
        $a = $antinode;
    }

    return $antinodes;
}

function get_pairs(array $locations): array
{
    $pairs = [];

    for ($i = 0; $i < count($locations) - 1; $i++) {
        for ($j = $i + 1; $j < count($locations); $j++) {
            $pairs[] = [$locations[$i], $locations[$j]];
        }
    }

    return $pairs;
}

function get_nodes(array $grid): array
{
    list($w, $h) = get_size($grid);
    $nodes = [];

    for ($y = 0; $y < $h; $y++) {
        for ($x = 0; $x < $w; $x++) {
            $node = $grid[$y][$x];
            if ($node === ".") continue;
            if (!isset($nodes[$node])) $nodes[$node] = [];
            $nodes[$node][] = [$x, $y];
        }
    }

    return $nodes;
}

main($data);
