<?php

use App\Http\Controllers\Honeypot;
use Illuminate\Support\Facades\Route;

Route::get('/{path}', Honeypot::class)->where('path', '.*');
