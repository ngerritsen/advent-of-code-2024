
<?php

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data)
{
  $grid = parse_grid($data);
  echo walk($grid) . PHP_EOL;
  echo find_loop_count($grid) . PHP_EOL;
}

function find_loop_count(array $grid)
{
  $count = 0;

  for ($y = 0; $y < count($grid); $y++) {
    for ($x = 0; $x < strlen($grid[$y]); $x++) {
      if (get($grid, [$x, $y]) !== ".") continue;
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
  $locations = [str($curr) => true];
  $positions = [str($curr) . str($dir) => true];

  while (true) {
    $next = add_dir($curr, $dir);
    $char = get($grid, $next);

    if ($char === "#") {
      $dir = rotate_dir($dir);
      continue;
    }

    if (empty($char)) return count($locations);

    $locations[str($next)] = true;
    $curr = $next;

    $pos = str($next) . "|" . str($dir);
    if (isset($positions[$pos])) return -1;
    $positions[$pos] = true;
  }
}

function get(array $grid, array $pos): string
{
  list($x, $y) = $pos;
  if ($y < 0 || $x < 0) return "";
  return $grid[$y][$x] ?? "";
}

function find_guard(array $grid): array
{
  foreach ($grid as $y => $line) {
    $x = strpos($line, "^");
    if ($x !== false) return [$x, $y];
  }
}

function str(array $coord): string
{
  return implode(",", $coord);
}

function rotate_dir(array $dir): array
{
  if ($dir[1] === 0) return [0, $dir[0]];
  else return [$dir[1] * -1, 0];
}

function add_dir(array $coord, array $dir)
{
  return [$coord[0] + $dir[0], $coord[1] + $dir[1]];
}

function parse_grid(string $data): array
{
  return explode("\n", trim($data));
}

main($data);
