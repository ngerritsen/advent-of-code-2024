<?php

require __DIR__ . "/../utils/grid.php";

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data)
{
    list($patterns, $designs) = parse_input($data);
    $max_pattern_len = max(array_map("strlen", array_keys($patterns)));
    $cache = [];

    $arrangements = array_map(fn($d) => get_arrangements($d, $patterns, $max_pattern_len, $cache), $designs);

    echo count(array_filter($arrangements)) . PHP_EOL;
    echo array_sum($arrangements) . PHP_EOL;
}

function get_arrangements(string $design, array $patterns, int $max_pattern_len, array &$cache): int
{
    $count = 0;

    if ($design === "") return 1;

    for ($i = 1; $i <= min($max_pattern_len, strlen($design)); $i++) {
        $pattern = substr($design, 0, $i);

        if (!array_key_exists($pattern, $patterns)) continue;

        $next = substr($design, strlen($pattern));

        if (!array_key_exists($next, $cache)) {
            $cache[$next] = get_arrangements($next, $patterns, $max_pattern_len, $cache);
        }

        $count += $cache[$next];
    }

    return $count;
}

function parse_input(string $data)
{
    list($t, $b) = explode("\n\n", trim($data));
    $patterns = array_flip(explode(", ", trim($t)));
    $designs = explode("\n", trim($b));
    return [$patterns, $designs];
}

main($data);