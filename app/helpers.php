<?php

if (!function_exists('ordinal')) {
    /**
     * Convert an integer to its ordinal string representation.
     * e.g. 1 => '1st', 2 => '2nd', 3 => '3rd', 4 => '4th'
     */
    function ordinal(int $n): string
    {
        $suffix = ['th', 'st', 'nd', 'rd'];
        $v = $n % 100;
        return $n . ($suffix[($v - 20) % 10] ?? $suffix[$v] ?? $suffix[0]);
    }
}
