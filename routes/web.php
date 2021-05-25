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
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::group(['middleware' => ['webUser']], function() {
        Route::group(['prefix' => 'purchases'], function () {
            Route::get('/index', 'PurchaseController@index');
            Route::get('/create', 'PurchaseController@create');
            Route::post('/store', 'PurchaseController@store');
            Route::post('/queryPurchases', 'PurchaseController@queryPurchases');
            Route::post('/queryPurchaseItems', 'PurchaseController@queryPurchaseItems');
            Route::post('/queryPurchaseItemsWithSupplier', 'PurchaseController@queryPurchaseItemsWithSupplier');

            
            Route::post('/paid', 'PurchaseController@paid');

        });

        Route::group(['prefix' => 'materials'], function () {
            Route::post('/queryData', 'MaterialController@queryData');
        });
        Route::group(['prefix' => 'purchase_items'], function () {
            Route::get('/index', 'PurchaseItemController@index');
        });
    });

});
require __DIR__.'/auth.php';
