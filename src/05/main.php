<?php

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data)
{
    list($rules, $updates) = parse_pages($data);
    list($valid_total, $fixed_total) = get_middle_number_totals($updates, $rules);

    echo $valid_total . PHP_EOL;
    echo $fixed_total . PHP_EOL;
}

function get_middle_number_totals(array $updates, array $rules): array
{
    $valid_total = 0;
    $fixed_total = 0;

    foreach ($updates as $update) {
        $sorted_update = sort_update($update, $rules);
        $middle = $sorted_update[floor(count($sorted_update) / 2)];
        if ($update === $sorted_update) $valid_total += $middle;
        if ($update !== $sorted_update) $fixed_total += $middle;
    }

    return [$valid_total, $fixed_total];
}


function get_rule_map(array $rules): array
{
    $map = [];

    foreach ($rules as $rule) {
        $map[$rule[0]] = $rule[1];
    }

    return $map;
}

function sort_update(array $update, array $rules): array
{
    usort($update, function ($a, $b) use ($rules) {
        if (isset($rules[$a][$b])) return -1;
        if (isset($rules[$b][$a])) return 1;
        return 0;
    });

    return $update;
}

function parse_pages(string $data): array
{
    $parts = explode("\n\n", trim($data));
    $rules = [];

    foreach (explode("\n", $parts[0]) as $line) {
        list($a, $b) = array_map(fn($c) => (int)$c, explode("|", $line));
        if (!isset($rules[$a])) $rules[$a] = [];
        $rules[$a][$b] = true;
    }

    $updates = array_map(
        fn($l) => array_map(fn($c) => (int)$c, explode(",", $l)),
        explode("\n", $parts[1])
    );

    return [$rules, $updates];
}

main($data);
