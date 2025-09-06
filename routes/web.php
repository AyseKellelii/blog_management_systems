<?php

use Illuminate\Support\Facades\Route;

// Vue SPA rotaları -> sadece /api ile başlamayan yolları yakalasın
Route::get('/{any}', function () {
    return view('app'); // Vue uygulaması
})->where('any', '^(?!api).*$');


