<?php

use Azuriom\Plugin\AuthSkinSlim\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/settings', [SettingsController::class, 'edit'])->name('settings');
Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
