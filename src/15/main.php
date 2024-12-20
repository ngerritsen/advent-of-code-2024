<?php

require __DIR__ . "/../utils/all.php";

$data = file_get_contents(__DIR__ . "/input.txt");

define("DIRS", [
    ">" => [1, 0],
    "v" => [0, 1],
    "<" => [-1, 0],
    "^" => [0, -1]
]);

function main(string $data): void
{
    list($grid, $moves) = parse_input($data);

    print_ln(execute_moves($grid, $moves));
    print_ln(execute_moves(scale_up_grid($grid), $moves));
}

function execute_moves(array $grid, array $moves): int
{
    $curr = find_robot($grid);

    foreach ($moves as $move) {
        list($curr, $grid) = try_move($grid, $curr, $move);
    }

    return total_box_gps_coords($grid);
}

function try_move(array $grid, array $curr, string $move): array
{
    $dir = DIRS[$move];
    $object = get_value($grid, $curr);
    $next = add_coord($curr, $dir);
    $obstacle = get_value($grid, $next);
    $wide_obstacle = $obstacle === "[" || $obstacle === "]";
    $is_horz_move = $dir[1] === 0;

    if ($wide_obstacle && !$is_horz_move) {
        $sibling_dir = $obstacle === "[" ? DIRS[">"] : DIRS["<"];
        $sibling = add_coord($next, $sibling_dir);

        list(, $next_grid) = try_move($grid, $next, $move);
        list(, $next_grid) = try_move($next_grid, $sibling, $move);

        $obstacle_a = get_value($next_grid, $next);
        $obstacle_b = get_value($next_grid, $sibling);

        if ($obstacle_a !== "." || $obstacle_b !== ".") return [$curr, $grid];

        $grid = $next_grid;
    }

    if ($obstacle === "O" || ($wide_obstacle && $is_horz_move)) {
        list(, $grid) = try_move($grid, $next, $move);
    }

    if ($obstacle === "#") return [$curr, $grid];

    $obstacle = get_value($grid, $next);

    if ($obstacle !== ".") return [$curr, $grid];

    set_value($grid, $curr, ".");
    set_value($grid, $next, $object);

    return [$next, $grid];
}

function total_box_gps_coords(array $grid)
{
    $total = 0;

    foreach ($grid as $y => $row) {
        for ($x = 0; $x < count($row); $x++) {
            $c = $row[$x];

            if ($c !== "O" && $c !== "[") continue;

            $total += $x + $y * 100;
            if ($c === "[") $x++;
        }
    }

    return $total;
}

function find_robot(array $grid): array
{
    foreach ($grid as $y => $row) {
        $x = array_search("@", $row);
        if ($x !== false) return [$x, $y];
    }
}

function scale_up_grid(array $grid): array
{
    return array_map(function ($row) {
        $next_row = [];

        foreach ($row as $x => $cell) {
            if ($cell === "O") array_push($next_row, "[", "]");
            else if ($cell === "@") array_push($next_row, "@", ".");
            else array_push($next_row, $cell, $cell);
        }

        return $next_row;
    }, $grid);
}

function parse_input(string $data): array {
    list($g, $m) = explode("\n\n", trim($data));
    $grid = parse_grid($g);
    $moves = str_split(implode("", explode("\n", trim($m))));

    return [$grid, $moves];
}

main($data);