<?php

if (!function_exists('progress')) {
    /**
     * Create a new instance of the Mtownsend\Progress\Progress class
     *
     * @return Mtownsend\Progress\Progress
     */
    function progress(...$steps)
    {
        return new \Mtownsend\Progress\Progress($steps);
    }
}

if (!function_exists('step')) {
    /**
     * Create a new instance of the Mtownsend\Progress\Step class
     *
     * @return Mtownsend\Progress\Step
     */
    function step($data, $name = '')
    {
        return new \Mtownsend\Progress\Step($data, $name);
    }
}

if (!function_exists('array_flatten')) {
    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  array  $array
     * @param  int  $depth
     * @return array
     */
    function array_flatten($array, $depth = INF) {
        $result = [];

        foreach ($array as $item) {
            if (!is_array($item)) {
                $result[] = $item;
            } else {
                $values = $depth === 1
                ? array_values($item)
                : array_flatten($item, $depth - 1);

                foreach ($values as $value) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }
}
