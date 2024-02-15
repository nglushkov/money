<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\TransferController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MoveController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\ExternalRateController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('auth')->group(function () {
    Route::resource('operations', OperationController::class);
    Route::resource('places', PlaceController::class);
    Route::resource('bills', BillController::class);
    Route::resource('currencies', CurrencyController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('exchanges', ExchangeController::class);
    Route::resource('transfers', TransferController::class);
    Route::resource('rates', RateController::class);
    Route::resource('external-rates', ExternalRateController::class);

    Route::get('/', [MoveController::class, 'index'])->name('home');

    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/get-cat-url', function () {
        $image = Cache::remember('catImage2', 60 * 60 * 24, function () {
            $response = Http::get('https://cataas.com/cat');
            $imageName = 'catImage.jpg';
            Storage::put($imageName, $response->body());
            return $imageName;
        });
    
        return response()->file(Storage::path($image));
    });
});

Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');