<?php

$data = file_get_contents(__DIR__ . "/input.txt");
$lines = explode("\n", $data);
$total = 0;

foreach ($lines as $line) {
  if (empty($line)) continue;
  preg_match("/^[a-z]*(\d)/", $line, $first);
  preg_match("/.*(\d)[a-z]*$/", $line, $last);
  $total += (int)($first[1] . $last[1]);
}

echo $total;
