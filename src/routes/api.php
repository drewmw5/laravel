<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Bus;

use App\Http\Controllers\CaptionsController;
use App\Http\Controllers\VideoController;


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

Route::get('/batch/{batchId}', function (Request $batchId) {
    // $data = $batchIds->all();
    // foreach($data as $batchId) {
        $response[] = Bus::findBatch($batchId);
    // }
    return $response;
});

Route::post('/captions', [CaptionsController::class, 'store']);
Route::get('/captions', [CaptionsController::class, 'show']);

Route::get('/videos', [VideoController::class, 'show']);