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
            Route::post('/delete', 'PurchaseController@delete');

            Route::post('/queryPurchases', 'PurchaseController@queryPurchases');
            Route::post('/queryPurchaseItems', 'PurchaseController@queryPurchaseItems');
            Route::post('/queryPurchaseItemsWithSupplier', 'PurchaseController@queryPurchaseItemsWithSupplier');
            Route::post('/queryPurchaseItemsWithReturns', 'PurchaseController@queryPurchaseItemsWithReturns');

            
            
            Route::post('/paid', 'PurchaseController@paid');
            Route::post('/bulk_import', 'PurchaseController@bulk_import');

        });
        Route::group(['prefix' => 'purchase_returns'], function () {
            Route::get('/index', 'PurchaseReturnController@index');
            Route::get('/create', 'PurchaseReturnController@create');
            Route::post('/store', 'PurchaseReturnController@store');
            Route::post('/queryData', 'PurchaseReturnController@queryData');
            Route::post('/queryPurchaseReturnWithItems', 'PurchaseReturnController@queryPurchaseReturnWithItems');

            Route::post('/delete', 'PurchaseReturnController@delete');
            
        });


        Route::group(['prefix' => 'purchase_items'], function () {
            Route::get('/index', 'PurchaseItemController@index');
        });

        Route::group(['prefix' => 'import_conversions'], function () {
            Route::post('/queryData', 'ImportConversionController@queryData');
        });

        Route::group(['prefix' => 'orders'], function () {
            Route::post('/bulk-import-qlieer-orders', 'OrderController@bulkImportQlieer');
        });

        Route::group(['prefix' => 'materials'], function () {
            Route::get('/{id}/edit', 'MaterialController@edit');
            Route::get('/index', 'MaterialController@index');
            Route::get('/create', 'MaterialController@create');
            Route::post('/store', 'MaterialController@store');
            Route::post('/update', 'MaterialController@update');
            Route::post('/delete', 'MaterialController@delete');
            Route::post('/queryData', 'MaterialController@queryData');
        });

        Route::group(['prefix' => 'transactions'], function () {
            Route::get('/index', 'TransactionController@index');
            Route::post('/queryData', 'TransactionController@queryData');
        });

        Route::group(['prefix' => 'products'], function () {
            Route::get('/{id}/edit', 'ProductController@edit');
            Route::get('/index', 'ProductController@index');
            // Route::get('/create', 'MaterialController@create');
            // Route::post('/store', 'MaterialController@store');
            Route::post('/update', 'ProductController@update');
            Route::post('/delete', 'ProductController@delete');
            Route::post('/queryData', 'ProductController@queryData');
        });

        Route::group(['prefix' => 'product_materials'], function () {
            Route::post('/queryData', 'ProductMaterialController@queryData');
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

        Route::group(['prefix' => 'closings'], function () {
            Route::get('/index', 'ClosingController@index');
            Route::post('/queryClosings', 'ClosingController@queryClosings');
            Route::post('/queryClosingWithItems', 'ClosingController@queryClosingWithItems');
            Route::post('/create', 'ClosingController@create');
        });

        Route::group(['prefix' => 'closing_items'], function () {
            Route::post('/queryItems', 'ClosingItemController@queryItems');
        });

        Route::group(['prefix' => 'material_sets'], function () {
            Route::get('/{id}/edit', 'MaterialSetController@edit');
            Route::get('/index', 'MaterialSetController@index');
            Route::get('/create', 'MaterialSetController@create');

            Route::post('/store', 'MaterialSetController@store');
            Route::post('/update', 'MaterialSetController@update');
            Route::post('/delete', 'MaterialSetController@delete');
            Route::post('/queryData', 'MaterialSetController@queryData');
        });
        
        Route::group(['prefix' => 'mis'], function () {
            Route::get('/index', 'MisController@index');
        });
    });

});
require __DIR__.'/auth.php';
