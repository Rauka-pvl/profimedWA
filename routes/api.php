<?php

use Illuminate\Support\Facades\Route;

Route::post('/WA-webhook', [App\Http\Controllers\WebHookWAController::class, 'webHook']);
