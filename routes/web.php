<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return "<p style='font-size: 18px; text-align: center;'>
                <span style='color: #00aa00;'>
                    The server is running successfully.<br/>
                </span>
                This acts as a pure API server, so there won't be anything to visit here; just use the API.
                <br/>
                <span style='color: red;'>
                    If you encounter any issues, please contact server administrator.
                </span>
            </p>";
});

require __DIR__.'/auth.php';
