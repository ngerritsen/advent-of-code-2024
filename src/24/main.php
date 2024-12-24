<?php

require __DIR__ . "/../utils/all.php";

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data): void
{
    [$wires, $gates] = parse_device($data);

    println(get_output($wires, $gates));
    println(get_swaps($wires, $gates));
}

function get_swaps(array $wires, array $gates): string
{
    $swapped = [];

    $max = count(get_all_z($wires));

    /**
     * These swaps are input specific, the rough process is:
     * 1. Remove these swaps, run the program, a faulty circuit will be printed.
     * 2. Figure out which wires need to be swapped (circuit diagram image will help a lot)
     * 3. Add the needed swap, run again, repeat. The final answer will be printed.
     */
    swap($gates, $swapped, "wpd", "z11");
    swap($gates, $swapped, "skh", "jqf");
    swap($gates, $swapped, "mdd", "z19");
    swap($gates, $swapped, "wts", "z37");

    for ($n = 0; $n < $max - 1; $n++) {
        $err = verify_circuit($gates, $n, $max);

        if ($err) {
            println("Error at {$n}: {$err}");
            print_circuit($gates, get_num_wire("z", $n));
            break;
        }
    }

    sort($swapped);

    return implode(",", $swapped);
}

function get_output(array $wires, array $gates): int
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

function print_circuit(array $gates, string $wire, int $depth = 0): void
{
    if (!isset($gates[$wire]) || $depth === 5) {
        println(str_repeat(" ", $depth * 4) . $wire);
        return;
    };

    $gate = $gates[$wire];
    println(str_repeat(" ", $depth * 4) . $wire  . " = {$gate[3]}:");

    print_circuit($gates, $gate[1], $depth + 1);
    print_circuit($gates, $gate[2], $depth + 1);
}

function verify_circuit(array $gates, int $n): string | false
{
    $xn = get_num_wire("x", $n);
    $z = $gates[get_num_wire("z", $n)];

    if ($z[3] !== "XOR") return "output is not an xor";
    if ($n === 0 && !has_input($z, $xn)) return "output does not source from the input";

    if ($n === 0) return false;

    list($a, $b) = get_prev_gates($gates, $z);

    $res = $a[3] === "XOR" ? $a : $b;
    $carry = $res === $b ? $a : $b;

    if ($res[3] !== "XOR") return "result is not an xor";
    if (!has_input($res, $xn)) return "result does not source from the input";
    if ($n === 1 && !has_input($res, $xn)) return "carry does not source from the input";

    if ($n === 1) return false;

    if ($carry[3] !== "OR") return "carry is not an or";

    list($pa, $pb) = get_prev_gates($gates, $carry);

    $pxn = get_num_wire("x", $n - 1);

    $recarry = has_input($pa, $pxn) ? $pa : $pb;

    if ($recarry[3] !== "AND") return "re-carry is not an and";
    if (!has_input($recarry, $pxn)) return "re-carry does not source from the input";

    $precarry = has_input($pa, $pxn) ? $pb : $pa;

    if ($precarry[3] !== "AND") return "pre-carry is not an and";

    list($ppa, $ppb) = get_prev_gates($gates, $precarry);

    $prevres = has_input($ppa, $pxn) ? $ppa : $ppb;

    if ($prevres[3] !== "XOR") return "prev result is not an XOR";
    if (!has_input($prevres, $pxn)) return "prev result does not source from the input";

    return false;
}

function get_num_wire(string $prefix, int $n): string
{
    $nn = str_pad((string)$n, 2, "0", STR_PAD_LEFT);
    return $prefix . $nn;
}

function has_input(array $gate, string $wire): bool
{
    return $gate[1] === $wire || $gate[2] === $wire;
}

function get_prev_gates(array $gates, array $gate)
{
    return [$gates[$gate[1]], $gates[$gate[2]]];
}

function get_gate(array $gates, string $wire, string $op = null): array
{
    foreach ($gates as $gate) {
        if (has_input($gate, $wire) && ($gate[3] === $op || !$op)) {
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

function swap(array &$gates, array &$swapped, string $a, string $b): void
{
    $ga = $gates[$a];
    $gb = $gates[$b];
    $ga[4] = $gb[4];
    $gb[4] = $ga[4];
    $gates[$a] = $gb;
    $gates[$b] = $ga;
    array_push($swapped, $a, $b);
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

    $lines = explode("\n", $b);

    sort($lines);

    foreach ($lines as $id => $line) {
        list($a, $op, $b,, $out) = explode(" ", $line);
        $gates[$out] = [$id, $a, $b, $op, $out];
        $wires[$a] = $wires[$a] ?? null;
        $wires[$b] = $wires[$b] ?? null;
        $wires[$out] = $wires[$out] ?? null;
    }

    return [$wires, $gates];
}

main($data);
