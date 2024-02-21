<?php

use Illuminate\Support\Facades\Route;

Route::get('/{user}', fn () => response('Not found!', 404));
