<?php

require __DIR__ . "/../utils/all.php";

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data): void
{
    list($reg, $prog) = parse_computer($data);

    print_ln(run($reg, $prog));
    print_ln(get_magic_a($reg, $prog));
}

function get_magic_a(array $reg, array $prog): int
{
    $prog_str = implode(",", $prog);
    $a = 0;

    while (true) {
        $ans = run(array_merge([$a], $reg), $prog);

        if (!str_ends_with($prog_str, $ans)) {
            $a++;
            continue;
        }

        if ($prog_str === $ans) return $a;

        $a = $a << 3;
    }
}

function run(array $reg, array $prog): string
{
    $res = [];

    for ($p = 0; $p < count($prog); $p += 2) {
        $i = $prog[$p];
        $v = $prog[$p + 1];

        if ($i === 0 || $i >= 6) $reg[$i % 5] = $reg[0] >> get_combo($reg, $v);
        if ($i === 1) $reg[1] = $reg[1] ^ $v;
        if ($i === 2) $reg[1] = get_combo($reg, $v) % 8;
        if ($i === 3 && $reg[0] !== 0) $p = $v - 2;
        if ($i === 4) $reg[1] = $reg[1] ^ $reg[2];
        if ($i === 5) $res[] = get_combo($reg, $v) % 8;
    }

    return implode(",", $res);
}

function get_combo(array $reg, int $val): int
{
    if ($val <= 3) return $val;
    return $reg[$val - 4];
}

function parse_computer(string $data)
{
    list($t, $b) = explode("\n\n", trim($data));
    $reg = array_map(fn($l) => (int)explode(": ", $l)[1], explode("\n", $t));
    $p = explode(": ", $b)[1];
    $prog = array_map("intval", explode(",", $p));
    return [$reg, $prog];
}

main($data);
