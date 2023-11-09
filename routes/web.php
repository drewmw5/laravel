<?php

use App\Http\Controllers\CaptionsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VideoController;
use App\Livewire\Home;
use App\Models\Video;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

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

Route::get('/', Home::class);

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'count' => DB::table('captions')->distinct()->count('video_id'),
        'totalVideos' => Video::count(),
        'videos' => Video::getAllWithJobBatches(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/captions', [CaptionsController::class, 'show']);
    Route::post('/playlist', [VideoController::class, 'storeMany']);
    Route::post('/video', [VideoController::class,'storeSingle']);
});

require __DIR__.'/auth.php';
