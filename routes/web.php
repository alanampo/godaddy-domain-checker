<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DomainController;

Route::get('/', function () {
    return view('domain-check');
});

Route::post('/check', [DomainController::class, 'check'])->name('check');
