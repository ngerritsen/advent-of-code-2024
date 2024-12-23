<?php

require __DIR__ . "/../utils/all.php";

$data = file_get_contents(__DIR__ . "/input.txt");

function main(string $data): void
{
    $nodes = parse_nodes($data);

    println(get_chief_groups($nodes));
    println(get_password($nodes));
}

function get_chief_groups(array $nodes): int
{
    return count(get_all_groups($nodes, 3, "t", true));
}

function get_password(array $nodes): string
{
    $max = max(array_map("count", $nodes));
    $groups = get_all_groups($nodes, $max);
    $by_size = array_values(array_flip($groups));
    return $by_size[count($by_size) - 1];
}

function get_all_groups(array $nodes, int $size, string $filter = "", bool $all = false): array
{
    $groups = [];

    foreach (array_keys($nodes) as $node) {
        if ($filter !== "" && !str_starts_with($node, $filter)) continue;

        foreach (get_groups($nodes, array_keys($nodes[$node]), [$node => 1], $size, $all) as $group) {
            $group = array_keys($group);
            sort($group);
            $groups[implode(",", $group)] = count($group);
            if (!$all) break 2;
        }

    }

    return $groups;
}

function get_groups(array $nodes, array $candidates, array $group, int $size, bool $all): array
{
    $groups = [];
    $next_size = count($group) + 1;

    while (!empty($candidates)) {
        $candidate = array_shift($candidates);

        foreach (array_keys($group) as $member) {
            if (!isset($nodes[$candidate][$member])) continue 2;
        }

        $next = array_merge($group, [$candidate => 1]);

        if ($size === $next_size) {
            $groups[] = $next;
            if (!$all) break;
            continue;
        }

        $groups = array_merge($groups, get_groups($nodes, $candidates, $next, $size, $all));
    }

    return $groups;
}

function parse_nodes(string $data): array
{
    $nodes = [];

    foreach (explode("\n", trim($data)) as $line) {
        list($a, $b) = explode("-", $line);
        $nodes[$a] = [...($nodes[$a] ?? []), $b => 1];
        $nodes[$b] = [...($nodes[$b] ?? []), $a => 1];
    }

    return $nodes;
}

main($data);