<?php


use Illuminate\Support\Facades\Route;

Route::get('/status', function () {
    return response()->json(['status' => 'OK']);
});



Route::get('/hello', function () {
    return response()->json(['message' => 'Hello API test!']);
});

