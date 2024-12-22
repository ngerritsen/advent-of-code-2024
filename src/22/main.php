<?php

require __DIR__ . "/../utils/all.php";

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data): void
{
    $nums = array_map("intval", explode("\n", $data));
    $nums = array_map(fn ($n) => generate_secret_numbers($n, 2000), $nums);

    println(get_total_2kth_num($nums));
    println(get_most_bananas($nums));
}

function get_most_bananas(array $nums): int
{
    $seqs = [];

    foreach ($nums as $buyer) {
        $seq = [];
        $visited_seqs = [];
        $prev = 0;

        foreach ($buyer as $i => $num) {
            $price = $num % 10;
            $diff = $price - $prev;
            $prev = $price;

            if ($i === 0) continue;

            $seq[] = $diff;

            if (count($seq) > 4) array_shift($seq);
            if (count($seq) < 4) continue;

            $key = implode(",", $seq); 

            if (isset($visited_seqs[$key])) continue;

            $seqs[$key] = ($seqs[$key] ?? 0) + $price;
            $visited_seqs[$key] = true;
        }
    }

    return max($seqs);
}

function get_total_2kth_num(array $secret_nums): int
{
    return array_reduce($secret_nums, fn ($t, $n) => $t + $n[count($n) - 1]);
}

function generate_secret_numbers(int $num, int $n): array
{
    $nums = [$num];

    for ($i = 0; $i < $n; $i++) {
        $nums[] = $num = generate_secret_num($num);
    }

    return $nums;
}

function generate_secret_num(int $num): int
{
    $num = (($num * 64) ^ $num) % 16777216;
    $num = (($num / 32) ^ $num) % 16777216;
    return (($num * 2048) ^ $num) % 16777216;
}

main($data);