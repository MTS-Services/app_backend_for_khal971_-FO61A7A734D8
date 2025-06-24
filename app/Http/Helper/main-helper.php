<?php

use Illuminate\Support\Carbon;


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
    return $time ? date('h:i A', strtotime($time)) : 'N/A';
}

function dateFormat($time)
{
    return $time ? date('d M, Y', strtotime($time)) : 'N/A';
}

function dateTimeFormat($time)
{
    return $time ? date('d M, Y h:i A', strtotime($time)) : 'N/A';
}
function timeFormatHuman($time)
{
    return Carbon::parse($time)->diffForHumans();
}


