<?php

namespace App\Http\Controllers;

use App\Services\BankService;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function __construct( protected BankService $bankService)
    {}


    public function transactions(){
        $type  = request()->query('type', 'PayTech');
        $count = request()->query('count');
        
        $data = $this->bankService->genrateTransaction($type , $count);

        return response()->json(['data' => $data]);

    }
}
