<?php

use App\Http\Controllers\BankController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Bank Routes
Route::get('get-transactions', [ BankController::class , 'transactions']);
Route::post('transactions-webhook', [ WalletController::class , 'receiveTransactions']);


//