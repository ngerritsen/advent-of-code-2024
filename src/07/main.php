<?php

require __DIR__ . "/../utils/all.php";

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data): void
{
    $equations = parse_equations($data);

    print_ln(get_valid_answers($equations, false));
    print_ln(get_valid_answers($equations, true));
}

function get_valid_answers(array $equations, bool $with_concat): int
{
    return array_reduce(
        $equations,
        fn($tot, $eq) => $tot += is_valid($eq, $with_concat) ? $eq[0] : 0
    );
}

function is_valid(array $equation, bool $with_concat): bool
{
    list($ans, $nums) = $equation;
    $queue = [[0, $nums[0]]];
    $len = count($nums);

    while (count($queue) > 0) {
        list($i, $curr) = array_pop($queue);
        if ($i === $len - 1 && $curr === $ans) return true;
        if ($i === $len - 1 || $curr > $ans) continue;
        $ni = $i + 1;
        $queue[] = [$ni, $curr * $nums[$ni]];
        $queue[] = [$ni, $curr + $nums[$ni]];
        if ($with_concat) $queue[] = [$ni, (int)($curr . $nums[$ni])];
    }

    return false;
}

function parse_equations(string $data): array
{
    return array_map(
        function (string $line): array {
            list($l, $r) = explode(": ", $line);
            $nums = array_map(fn($c) => (int)$c, explode(" ", $r));
            return [(int)$l, $nums];
        },
        array_filter(explode("\n", trim($data)))
    );
}

main($data);
