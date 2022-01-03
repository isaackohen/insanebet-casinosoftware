<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

/**
 * Configure the web setup for the application.
 *
 * @avatar hash
 * @vue capture
 */

/*

Route::get('/what-is-my-ip', function(){
    return request()->ip();
});

// ^ Only for maintence by ip

*/

Route::get('/avatar/{hash}', [MainController::class, 'avatar']);
Route::get('/{vue_capture?}', [MainController::class, 'main'])->where('vue_capture', '[\\/\\w\\:.-]*');

Route::post('/broadcasting/auth', [MainController::class, 'broadcasting']);
