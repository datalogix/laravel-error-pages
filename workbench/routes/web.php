<?php

use Illuminate\Support\Facades\Route;

Route::get('/boom/{code}', fn (int $code) => abort($code));
