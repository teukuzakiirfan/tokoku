<?php

use Illuminate\Support\Carbon;

if (! function_exists('rp')) {
    function rp($amount): string
    {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }
}

if (! function_exists('tgl')) {
    function tgl($date, string $format = 'd M Y, H:i'): string
    {
        return Carbon::parse($date)->locale('id')->translatedFormat($format);
    }
}
