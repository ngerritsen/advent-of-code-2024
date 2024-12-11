<?php

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data)
{
  $stones = parse_stones($data);

  echo blink($stones, 25) . PHP_EOL;
  echo blink($stones, 75) . PHP_EOL;
}

function blink(array $stones, int $times): int
{
  for ($n = 1; $n <= $times; $n++) {
    $next = [];

    foreach ($stones as $num => $count) {
      foreach (get_next_nums($num) as $nn) {
        $next[$nn] = ($next[$nn] ?? 0) + $count;
      }
    }

    $stones = $next;
  }

  return array_sum($stones);
}

function get_next_nums(int $num): array
{
  $str = (string)$num;

  if (strlen($str) % 2 === 0) {
    $half = strlen($str) / 2;
    return [(int)substr($str, 0, $half), (int)substr($str, $half)];
  }

  return [$num === 0 ? 1 : $num * 2024];
}

function parse_stones(string $data): array
{
  $stones = [];

  foreach (explode(" ", trim($data)) as $s) {
    $stones[$s] = 1;
  }

  return $stones;
}

main($data);
