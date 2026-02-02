<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Mail\Markdown;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/readme', function () {
    $markdownContent = File::get(base_path('README.md'));
    $htmlContent = Markdown::parse($markdownContent);

    return view('readme', ['content' => $htmlContent]);
});
