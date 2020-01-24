<?php

if (!function_exists('es')) {

    /**
     * Helper to return error source for custom errors
     * 
     * @return mixed
     */
    function es($source = '')
    {
        return ['source' => $source];
    }

}

if (!function_exists('get_memory_usage')) {

    /**
     * Helper to get current memory usage
     * 
     * @realUsage boolean Set this to TRUE to get total memory allocated from system, including unused pages. If not set or FALSE only the used memory is reported.
     * 
     * @return string get current memory usage in mb
     */
    function get_memory_usage($realUsage = false)
    {
        return round((memory_get_usage($realUsage)/1048576),2).' MB';
    }

}