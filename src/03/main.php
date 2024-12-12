<?php

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data)
{
    $instructions = parse_instructions($data);

    echo calculate_total($instructions, false) . PHP_EOL;
    echo calculate_total($instructions, true) . PHP_EOL;
}

function calculate_total(array $instructions, bool $use_skip): int
{
    $total = 0;
    $skip = false;

    foreach ($instructions as $instruction) {
        if ($instruction == "do()") $skip = false;
        if ($instruction == "don't()") $skip = true;
        if ($instruction[0] !== "m") continue;
        if ($skip && $use_skip) continue;
        preg_match_all("/\d+/", $instruction, $nums);
        $total += $nums[0][0] * $nums[0][1];
    }

    return $total;
}

function parse_instructions(string $data): array
{
    preg_match_all("/(mul\(\d+,\d+\))|(don\'t\(\))|(do\(\))/", $data, $matches);
    return $matches[0];
}


main($data);
