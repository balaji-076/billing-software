<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\InvoiceController;


Route::prefix('v1')->group(function () {
    
    // Store Management Endpoints
    Route::get('/stores', [StoreController::class, 'index']);

    // Product Catalog Endpoints
    Route::get('/products', [ProductController::class, 'paginateStoreProducts']);
    Route::get('/products/{id}', [ProductController::class, 'fetchProductDetails']);
    Route::post('/products', [ProductController::class, 'persistProductRecord']);
    Route::match(['put', 'patch'], '/products/{id}', [ProductController::class, 'updateProductRecord']);
    Route::delete('/products/{id}', [ProductController::class, 'removeProductRecord']);


    Route::get('/invoices', [InvoiceController::class, 'paginateStoreInvoices']);
    Route::post('/invoices', [InvoiceController::class, 'processAndStoreInvoice']);
});


