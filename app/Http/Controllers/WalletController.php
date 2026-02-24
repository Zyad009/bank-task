<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReceiveRequest;
use App\Http\Requests\SendingMoneyRequest;
use App\Models\Setting;
use App\Services\GenerateXml;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(
        protected TransactionService $TransactionService,
        protected GenerateXml $generateXmlService,
    ) {}

    public function receiveTransactions(ReceiveRequest $request)
    {
        $data = $request->validated();

        $this->TransactionService->receiveTransactions($data['data']);

        return response()->json(['message' => 'Transactions received successfully']);
    }
    public function changeIngestion()
    {

        $ingestionSetting = Setting::where('key', 'ingestion')->first();
        $newStatus = !$ingestionSetting->value;
        $ingestionSetting->update(['value' => $newStatus]);
        if ($newStatus) {
            $this->TransactionService->ingestionTransactions();
        }
        return response()->json(['message' => 'change Successfully']);
    }

    public function sendingMoney(SendingMoneyRequest $request){
        $data = $request->validated();
        $xml = $this->generateXmlService->generate($data);

        // return response($xml, 200)->header('Content-Type', 'application/xml; charset=utf-8');
        return response($xml, 200);
    }
}
