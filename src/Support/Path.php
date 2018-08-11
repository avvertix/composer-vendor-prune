<?php

namespace Avvertix\VendorPrune\Support;

final class Path
{
    /**
     * Path to a folder with respect to the current working directory
     * 
     * @param string $folder
     * @return string
     */
    public static function path($folder)
    {
        return rtrim(static::workingDir(), '/') . "/$folder";
    }
    
    /**
     * The current working directory
     * 
     * @return string
     */
    public static function workingDir()
    {
        $insidePhar = starts_with(__DIR__, 'phar://');

        $base = $insidePhar ? \Phar::running(false) : realpath(__DIR__.'/../../');

        $scriptfilename = str_after(str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']), './');
        
        return str_replace_last($scriptfilename, '', $base);
    }

    
}


