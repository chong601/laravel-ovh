<?php

use App\Http\Controllers\WebApi\Ip\Reverse;
use App\Http\Controllers\WebApi\Ip\VirtualMac;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// IP Address
Route::prefix('/ip')->name('ip.')->group(function () {
    // IP Address by IP
    Route::get('/', [Ip::class, 'all'])->name('all');
    Route::prefix('/{ip}')->group(function () {
        Route::get('/', [Ip::class, 'get'])->name('get');
        // Reverse Domain
        Route::prefix('/reverse')->name('reverse.')->group(function () {
            Route::get('/', [Reverse::class, 'all'])->name('all');
            Route::get('/{ipReverse}', [Reverse::class, 'get'])->name('get');
        });
    });
});
Route::get('/ip/reverseDetail/{serviceName}', [Reverse::class, 'getIpReverse']);
Route::get('/ip/virtualMac/{serviceName}', [VirtualMac::class, 'getVirtualMac']);

