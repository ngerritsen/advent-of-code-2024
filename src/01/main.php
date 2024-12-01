<?php

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data)
{
  [$left, $right] = parse($data);

  echo get_total_diff($left, $right) . PHP_EOL;
  echo get_total_similarity_score($left, $right) . PHP_EOL;
}

function get_total_similarity_score(array $left, array $right): int
{
  $score = 0;
  $occurences = [];

  foreach ($right as $id) {
    if (empty($occurences[$id])) $occurences[$id] = 0;
    $occurences[$id]++;
  }

  foreach ($left as $id) {
    $mul = $occurences[$id] ?? 0;
    $score += $id * $mul;
  }

  return $score;
}

function get_total_diff(array $left, array $right): int
{
  $total = 0;

  sort($left);
  sort($right);

  foreach ($left as $i => $id) {
    $total += abs($id - $right[$i]);
  }

  return $total;
}

function parse(string $data): array
{
  $lines = explode("\n", $data);
  $left = [];
  $right = [];

  foreach ($lines as $line) {
    if (empty($line)) continue;
    $parts = explode(" ", $line);
    $left[] = (int)$parts[0];
    $right[] = (int)end($parts);
  }

  return [$left, $right];
}


main($data);
