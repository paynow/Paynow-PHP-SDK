<?php

/**
 * Check if a key exists in an array (wrapper for array_key_exists)
 * @param array $array The array to check
 * @param mixed $key The key to find
 * @return bool
 */
function arr_has($array, $key) {
    return array_key_exists($key, $array);
}

/**
 * Check if a value exists in an array (wrapper for in_array)
 * @param array $array The array to check
 * @param mixed $value The value to find
 * @return bool
 */
function arr_contains($array, $value)
{
    return in_array($value, $array);
}
