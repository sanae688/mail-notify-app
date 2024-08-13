<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\WebhookController;

Route::get('/loginController', [LoginController::class, 'requestUserAuthorization']);

Route::get(env('SMAREGI_REDIRECT_URL'), [LoginController::class, 'handleOAuthCallback']);

Route::post(env('SMAREGI_WEBHOOK_URL'), WebhookController::class);
