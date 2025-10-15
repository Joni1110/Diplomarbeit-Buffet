<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/kontakt', function () {
    return view('kontakt');
});

Route::get('/ueberuns', function () {
    return view('ueberuns');
});

Route::get('/bestellungen', function () {
    return view('bestellungen');
});

Route::get('/home', function () {
    return view('home');
});
