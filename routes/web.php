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
            Route::post('/bulk_import', 'PurchaseController@bulk_import');

        });

        Route::group(['prefix' => 'materials'], function () {
            Route::get('/index', 'MaterialController@index');
            Route::post('/queryData', 'MaterialController@queryData');
        });
        Route::group(['prefix' => 'purchase_items'], function () {
            Route::get('/index', 'PurchaseItemController@index');
        });

        Route::group(['prefix' => 'suppliers'], function () {
            Route::get('/{id}/edit', 'SupplierController@edit');
            Route::get('/index', 'SupplierController@index');
            Route::get('/create', 'SupplierController@create');

            Route::post('/store', 'SupplierController@store');
            Route::post('/update', 'SupplierController@update');
            Route::post('/delete', 'SupplierController@delete');
            Route::post('/queryData', 'SupplierController@queryData');
        });
        
        Route::group(['prefix' => 'mis'], function () {
            Route::get('/index', 'MisController@index');
        });
    });

});
require __DIR__.'/auth.php';
