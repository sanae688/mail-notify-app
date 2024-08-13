<?php

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

Route::get('/loginController', [LoginController::class, 'requestUserAuthorization']);

Route::get(env('SMAREGI_REDIRECT_URL'), [LoginController::class, 'handleOAuthCallback']);
