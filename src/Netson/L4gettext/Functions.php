<?php

/**
 * only create function if it doesn't already exist
 */
if (!function_exists('_n'))
{
    /**
     * function creates a shortcut for the ngettext() function
     *
     * @param string $msg1
     * @param string $msg2
     * @param integer $n
     * @return string
     */
    function _n ($msg1, $msg2, $n)
    {
        // return regular function result
        return ngettext($msg1, $msg2, $n);
    }
}
function getGlobPattern($levels)
{
    $pattern = "*";
    for ($i = 1; $i <= $levels; $i++)
    {
    if ($pattern != "")
        $pattern .= ",";
        $pattern .= str_repeat("*/", $i);
    }
    return $pattern;
}

?>