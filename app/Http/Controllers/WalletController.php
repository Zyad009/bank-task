<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReceiveRequest;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        protected TransactionService $TransactionService
    ) 
    {}
    public function receiveTransactions(ReceiveRequest $request){
        $data = $request->validated();

        $this->TransactionService->receiveTransactions($data['data']);
        
        return response()->json(['message' => 'Transactions received successfully']);

    }
}
