<?php

require __DIR__ . "/../utils/all.php";

$data = file_get_contents(__DIR__ . "/input.txt");

define("WIDTH", 101);
define("HEIGHT", 103);

function main(string $data): void
{
    $robots = parse_robots($data);

    print_ln(wait($robots, 100)[0]);
    print_ln(find_easter_egg($robots));
}

function find_easter_egg($robots): int
{
    $clue = str_repeat("#", floor(WIDTH * 0.2));

    for ($s = 0; $s < 1e5; $s++) {
        $grid = wait($robots, $s)[1];
        if (strpos(str_grid($grid), $clue) > 0) return $s;
    }
}

function wait(array $robots, int $s): array
{
    $grid = create_grid(WIDTH, HEIGHT);
    $quadrants = [0, 0, 0, 0];
    $mid_y = floor(HEIGHT / 2);
    $mid_x = floor(WIDTH / 2);

    foreach ($robots as $robot)
    {
        list($x, $y) = walk($robot, $s);

        $grid[$y][$x] = "#";

        if ($x < $mid_x && $y < $mid_y) $quadrants[0] += 1;
        if ($x > $mid_x && $y < $mid_y) $quadrants[1] += 1;
        if ($x < $mid_x && $y > $mid_y) $quadrants[2] += 1;
        if ($x > $mid_x && $y > $mid_y) $quadrants[3] += 1;
    }

    $safety_factor = $quadrants[0] * $quadrants[1] * $quadrants[2] * $quadrants[3];

    return [$safety_factor, $grid];
}

function walk(array $robot, int $s): array
{
    list($p, $v) = $robot;

    $x = walk_axis($p[0], $v[0], WIDTH, $s);
    $y = walk_axis($p[1], $v[1], HEIGHT, $s);

    return [$x, $y];
}

function walk_axis(int $curr, int $v, int $max, int $n): int
{
    $d = ($curr + ($v * $n)) % $max;
    return $d < 0 ? $max + $d : $d;
}

function parse_robots(string $data): array
{
    return array_map("parse_robot", explode("\n", trim($data)));
}

function parse_robot(string $line): array
{
    list($p, $v) = explode(" ", $line);
    return [parse_param($p), parse_param($v)];
}

function parse_param(string $str): array
{
    return array_map("intval", explode(",", substr($str, 2)));
}

main($data);
