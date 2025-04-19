<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;

Route::get('/', [WeatherController::class, 'index']);
Route::get('/api/weather/current', [WeatherController::class, 'getCurrentWeather']);
Route::get('/api/weather/forecast', [WeatherController::class, 'getForecast']);
