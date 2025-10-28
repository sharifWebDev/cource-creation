<?php

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

function decodeJSON($jsonData)
{
    return is_array($jsonData) ? $jsonData : json_decode($jsonData ?? "{}", true);
}

if (!function_exists('format_date')) {
    function format_date($date)
    {
        if (!$date) {
            return 'N/A';
        }

        return Carbon::parse($date)->format('d M Y');
    }
}

if (! function_exists('limitText')) {
    function limitText(string $text, int $limit = 200, string $end = '...'): string
    {
        return Str::limit($text, $limit, $end);
    }
}
