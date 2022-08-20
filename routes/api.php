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

Route::get('/ip/reverseDetail/{serviceName}', [Reverse::class, 'getIpReverse']);
Route::get('/ip/virtualMac/{serviceName}', [VirtualMac::class, 'getVirtualMac']);

