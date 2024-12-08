<?php

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data) {
  $grid = parse_grid($data);
  $nodes = get_nodes($grid);
  echo get_antinode_count($grid, $nodes, false) . PHP_EOL;
  echo get_antinode_count($grid, $nodes, true) . PHP_EOL;
}

function get_antinode_count(array $grid, array $nodes, bool $unlimited): int
{
  list($w, $h) = get_size($grid);
  $antinodes = [];

  foreach ($nodes as $locations) {
    $pairs = get_pairs($locations);

    foreach ($pairs as $pair) {
      list($a, $b) = $pair;

      foreach (get_antinodes($a, $b, $w, $h, $unlimited) as $node) {
        $antinodes[str($node)] = true;
      }

      foreach (get_antinodes($b, $a, $w, $h, $unlimited) as $node) {
        $antinodes[str($node)] = true;
      }
    }
  }

  return count($antinodes);
}

function get_antinodes(array $a, array $b, int $w, int $h, bool $unlimited): array
{
  $antinodes = [];
  $step = sub_coord($a, $b);

  if ($unlimited) $antinodes[] = $a;

  while (true) {
    $antinode = add_coord($a, $step);
    if (!is_in_bounds($antinode, $w, $h)) break;
    $antinodes[] = $antinode;
    if (!$unlimited) break;
    $a = $antinode;
  } 

  return $antinodes;
}

function is_in_bounds(array $coord, int $w, int $h)
{
  list($x, $y) = $coord;
  return $x >= 0 && $y >= 0 && $x < $w && $y < $h;
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

function str(array $coord): string
{
  return implode(",", $coord);
}

function sub_coord(array $a, array $b): array
{
  return [$a[0] - $b[0], $a[1] - $b[1]];
}

function add_coord(array $a, array $b)
{
  return [$a[0] + $b[0], $a[1] + $b[1]];
}

function get_size(array $grid): array
{
  return [strlen($grid[0]), count($grid)];
}

function parse_grid(string $data): array
{
  return explode("\n", trim($data));
}

main($data);