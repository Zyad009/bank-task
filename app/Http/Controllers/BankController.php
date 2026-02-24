<?php

namespace App\Http\Controllers;

use App\Services\BankService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BankController extends Controller
{
    public function __construct(protected BankService $bankService, protected TransactionService $transactionService) {}


    public function transactions()
    {
        $type  = request()->query('type', 'PayTech');
        $count = request()->query('count');
        // عملته بس عشان لو عايزه يكمل ويعمل العمليات او لا ويرج الداتا فقط وده بس عشان التجربه و الاختبار 
        $webhook = request()->query('webhook', 0);

        try {
            $data = $this->bankService->genrateTransaction($type, $count);

            if ($webhook) {

                //حاولت بس حصل مشكله عشان السيرفر المحلي مشعارف يشغل 2 ريكوست مع بعض
                //$url = route('transactions.webhook');
                // $response = Http::post($url, [
                //     'data' => $data,
                // ]);

                $this->transactionService->receiveTransactions($data);

                return response()->json([
                    'message' => 'Webhook simulated locally',
                    'count'   => count($data),
                ]);
            }
            return successResponse($data, 'Transactions generated successfully');
        } catch (\Throwable $th) {
            Log::error('Error generating transactions: ' . $th->getMessage());
            return errorResponse( message: 'Error generating transactions',  th:$th);
        }
    }
}
