<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;


function sendResponse($status, $message, $data = null, $statusCode = 200, $additional = null)
{
    $responseData = [
        'success' => $status,
        'message' => $message,
        'data' => $data
    ];
    if (!empty($additional) && is_array($additional)) {
        $responseData = array_merge($responseData, $additional);
    }
    return response()->json($responseData, $statusCode);
}

function timeFormat($time)
{
    return $time ? date('h:i A', strtotime($time)) : null;
}

function dateFormat($time)
{
    return $time ? date('d M, Y', strtotime($time)) : null;
}

function dateTimeFormat($time)
{
    return $time ? date('d M, Y h:i A', strtotime($time)) : null;
}
function timeFormatHuman($time)
{
    return Carbon::parse($time)->diffForHumans();
}


function storage_url($urlOrArray)
{
    $image = asset('default_img/no_img.jpg');
    if (is_array($urlOrArray) || is_object($urlOrArray)) {
        $result = '';
        $count = 0;
        $itemCount = count($urlOrArray);
        foreach ($urlOrArray as $index => $url) {

            $result .= $url ? (Str::startsWith($url, 'https://') ? $url : asset('storage/' . $url)) : $image;


            if ($count === $itemCount - 1) {
                $result .= '';
            } else {
                $result .= ', ';
            }
            $count++;
        }
        return $result;
    } else {
        return $urlOrArray ? (Str::startsWith($urlOrArray, 'https://') ? $urlOrArray : asset('storage/' . $urlOrArray)) : $image;
    }
}

function allowLangs(): array
{
    return ['es', 'ar', 'it', 'en'];
}

function defaultLang(): string
{
    return 'en';
}


function translation($translations)
{
    $lang = request()->header('Accept-Language', defaultLang());
    $translation = $translations->where('language', $lang)->first();
    if (!$translation) {
        $translation = $translations->where('language', 'en')->first();
    }
    return $translation;
}






