<?php

use App\Http\Controllers\FileController;
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

Route::post('/uploadFile', [FileController::class, 'uploadFile']);
Route::post('/setNameFile', [FileController::class, 'setNameFile']);
Route::post('/deleteFile', [FileController::class, 'deleteFile']);
Route::post('/clearTableFiles', [FileController::class, 'clearTableFiles']);
Route::post('/setAllFilesNames', [FileController::class, 'setAllFilesNames']);
Route::get('/getFiles', [FileController::class, 'getFiles']);
Route::get('/getFilesSortingName', [FileController::class, 'getFilesSortingName']);
Route::get('/getFilesSortingDate', [FileController::class, 'getFilesSortingDate']);
Route::get('/downloadFile', [FileController::class, 'downloadFile']);
