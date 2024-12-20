<?php

require __DIR__ . "/../utils/all.php";

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data): void
{
    $reports = parse_reports($data);

    print_ln(get_safe_count($reports, false));
    print_ln(get_safe_count($reports, true));
}

function get_safe_count(array $reports, bool $tolerate_bad): int
{
    $count = 0;

    foreach ($reports as $report) {
        $is_safe = is_safe($report);

        if ($is_safe) $count++;
        if ($is_safe || !$tolerate_bad) continue;

        for ($i = 0; $i < count($report); $i++) {
            if (!is_safe(without($report, $i))) continue;
            $count++;
            break;
        }
    }

    return $count;
}

function is_safe(array $report): bool
{
    foreach ($report as $i => $curr) {
        if ($i === 0) continue;
        $prev = $report[$i - 1];
        $diff = $prev - $curr;
        if ($diff === 0 || abs($diff) > 3) return false;
        if ($i <= 1) continue;
        $prev_diff = $report[$i - 2] - $prev;
        if (($diff <=> 0) !== ($prev_diff <=> 0)) return false;
    }

    return true;
}

function without(array $report, int $index): array
{
    return array_values(array_filter($report, fn($i) => $i !== $index, ARRAY_FILTER_USE_KEY));
}

function parse_reports(string $data): array
{
    return array_map(
        fn($line) =>
        array_map(fn($n) => (int)$n, explode(" ", $line)),
        array_filter(explode("\n", $data))
    );
}

main($data);
