<?php

if (!function_exists('successResponse')) {
  function successResponse($data = [], $message = 'Success', $statusCode = 200)
  {
    return response()->json([
      'message' => $message,
      'data' => $data
    ], $statusCode);
  } 
}

if(!function_exists('errorResponse')) {
  function errorResponse( $data = [] , $message = 'error' ,  $statusCode = 500 , $th = null)
  {
    if($th && $th instanceof \Throwable) {
      $message = $th->getMessage();
      if(is_int($th->getCode()) && $th->getCode() >= 400 && $th->getCode() < 600) {
        $statusCode = $th->getCode() ?: 500;
      }
    }

    return response()->json([
      'message' => $message,
      'data' => $data
    ], $statusCode);

  }

}