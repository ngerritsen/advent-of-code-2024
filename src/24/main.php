<?php

require __DIR__ . "/../utils/all.php";

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data): void
{
    [$wires, $gates] = parse_device($data);

    println(get_ans($wires, $gates));
}

function get_ans(array $wires, array $gates): int
{
    while (!has_all_z($wires)) {
        foreach ($gates as $gate) {
            list(, $a, $b, $op, $out) = $gate;
            if ($wires[$out] !== null) continue;
            if ($wires[$a] === null || $wires[$b] === null) continue;
            if ($op === "AND") $wires[$out] = $wires[$a] & $wires[$b];
            if ($op === "OR") $wires[$out] = $wires[$a] | $wires[$b];
            if ($op === "XOR") $wires[$out] = $wires[$a] ^ $wires[$b];
        }
    }

    $zs = get_all_z($wires);

    krsort($zs);

    return bindec(implode("", $zs));
}

function is_input_for(array $gate, string $wire): bool
{
    return $gate[1] === $wire || $gate[2] === $wire;
}

function get_gate(array $gates, string $wire, string $op = null): array
{
    foreach ($gates as $gate) {
        if (is_input_for($gate, $wire) && ($gate[3] === $op || !$op)) {
            return $gate;
        }
    }

    return [];
}

function get_gate_to(array $gates, string $out): array
{
    foreach ($gates as $gate) {
        if ($gate[4] === $out) return $gate;
    }
}

function has_all_z(array $wires): bool
{
    foreach (get_all_z($wires) as $val) {
        if ($val === null) return false;
    }

    return true;
}

function get_all_z(array $wires): array
{
    $res = [];

    foreach ($wires as $name => $val) {
        if (str_starts_with($name, "z")) $res[$name] = $val;
    }

    return $res;
}


function parse_device(string $data): array
{
    $wires = [];
    $gates = [];

    list($t, $b) = explode("\n\n", trim($data));

    foreach (explode("\n", $t) as $line) {
        list($name, $val) = explode(": ", $line);
        $wires[$name] = (int)$val;
    }

    foreach (explode("\n", $b) as $id => $line) {
        list($a, $op, $b,, $out) = explode(" ", $line);
        $gates[$id] = [$id, $a, $b, $op, $out];
        $wires[$a] = $wires[$a] ?? null;
        $wires[$b] = $wires[$b] ?? null;
        $wires[$out] = $wires[$out] ?? null;
    }

    return [$wires, $gates];
}

function str(array $gate)
{
    return "({$gate[0]}) {$gate[1]} {$gate[3]} {$gate[2]} -> {$gate[4]}";
}

main($data);
