<?php


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


