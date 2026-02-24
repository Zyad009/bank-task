<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Bank Routes
Route::get('get-transactions', [ BankController::class , 'transactions']);
Route::patch('change-ingestion' , [WalletController::class , 'changeIngestion']);
Route::post('transactions-webhook', [WalletController::class, 'receiveTransactions'])
  ->name('transactions.webhook');

Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

Route::post('sending-money', [WalletController::class, 'sendingMoney']);
//