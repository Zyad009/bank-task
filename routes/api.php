<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('get-transactions', [BankController::class, 'transactions']);

Route::controller(WalletController::class)->group(function () {
  Route::post('transactions-webhook', 'receiveTransactions')->name('transactions.webhook');
  Route::patch('change-ingestion', 'changeIngestion');
  Route::post('sending-money',  'sendingMoney');
});
