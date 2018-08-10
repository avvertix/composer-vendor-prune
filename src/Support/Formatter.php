<?php

namespace Avvertix\VendorPrune\Support;

final class Formatter
{
    public static function size($bytes, $decimals = 2)
    {
        $size = ['B','KB','MB','GB','TB','PB','EB','ZB','YB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)).@$size[$factor];
    }

    public static function gain($currentSize, $prunedSize, $decimals = 2)
    {
        $percent = ($prunedSize*100)/$currentSize;

        return sprintf("%1\$s => %2\$s %3$.{$decimals}f", 
            static::size($currentSize, $decimals),
            static::size($prunedSize, $decimals),
            $percent) . '%';
    }
}


