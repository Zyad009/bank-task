<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReceiveRequest;
use App\Http\Requests\SendingMoneyRequest;
use App\Models\Setting;
use App\Services\GenerateXml;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    public function __construct(
        protected TransactionService $TransactionService,
        protected GenerateXml $generateXmlService,
    ) {}

    public function receiveTransactions(ReceiveRequest $request)
    {
        try {
            $data = $request->validated();

            $this->TransactionService->receiveTransactions($data['data']);

            return response()->json(['message' => 'Transactions received successfully']);
        } catch (\Throwable $th) {
            Log::error('WalletController::receiveTransactions error: ' . $th->getMessage());
            return response()->json(['message_error' => $th->getMessage()], $th->getCode() ?: 500);
        }
    }

    public function changeIngestion()
    {
        try {
            $ingestionSetting = Setting::where('key', 'ingestion')->first();
            $newStatus = !$ingestionSetting->value;
            $ingestionSetting->update(['value' => $newStatus]);

            if ($newStatus) {
                $this->TransactionService->ingestionTransactions();
            }

            return response()->json(['message' => 'change Successfully']);
        } catch (\Throwable $th) {
            Log::error('WalletController::changeIngestion error: ' . $th->getMessage());
            return response()->json(['message_error' => $th->getMessage()], 400);
        }
    }

    public function sendingMoney(SendingMoneyRequest $request)
    {
        try {
            $data = $request->validated();
            $xml  = $this->generateXmlService->generate($data);

            // return response($xml, 200)->header('Content-Type', 'application/xml; charset=utf-8');
            return response($xml, 200);
        } catch (\Throwable $th) {
            Log::error('WalletController::sendingMoney error: ' . $th->getMessage());
            return response()->json(['message_error' => $th->getMessage()], 400);
        }
    }
}
