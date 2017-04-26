<?php declare(strict_types = 1);

namespace Alcaeus\DocblockAnalyzer\Functions;

/**
 * @param array $array
 *
 * @return array
 */
function array_flatten(array $array): array
{
    return count($array) > 0 ? array_merge(...array_values($array)) : [];
}
