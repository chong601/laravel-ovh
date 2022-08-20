<?php

use App\Http\Controllers\WebApi\Dedicated\Server as DedicatedServer;
use App\Http\Controllers\WebApi\Dedicated\Server\VirtualMac;
use App\Http\Controllers\WebApi\Dedicated\Server\VirtualMac\VirtualAddress;
use App\Http\Controllers\WebApi\Ip;
use App\Http\Controllers\WebApi\Ip\Reverse;
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

Route::prefix('/dedicated')->name('dedicated.')->group(function () {
    Route::prefix('/server')->name('server.')->group(function() {
        Route::get('/', [DedicatedServer::class, 'all'])->name('all');
        Route::prefix('/{serviceName}')->group(function () {
            Route::get('/', [DedicatedServer::class, 'get'])->name('get');
            Route::prefix('/virtualMac')->name('virtualMac.')->group(function () {
                Route::get('/', [VirtualMac::class, 'all'])->name('all');
                Route::prefix('/{macAddress}')->group(function () {
                    Route::get('/', [VirtualMac::class, 'get'])->name('get');
                    Route::prefix('/virtualAddress')->name('virtualAddress.')->group(function () {
                        Route::get('/', [VirtualAddress::class, 'all'])->name('all');
                        Route::get('/{ipAddress}', [VirtualAddress::class, 'get'])->name('get');
                    });
                });
            });
        });
    });
});
