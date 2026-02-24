<?php

namespace App\Services;

use App\Models\PendingTransaction;
use App\Models\Setting;
use App\Models\Transaction;
use Carbon\Carbon;

use function Symfony\Component\String\s;

class TransactionService
{

  public array $transations;

  public function receiveTransactions(array $transactions)
  {
    $availableAction = Setting::where('key', 'ingestion')->first()->value;

    if ($availableAction) {
      $this->paymentProcessing($transactions);
    } else {
      $this->pendingTransactions($transactions);
    }
  }


  private function PayTechMethodData($transaction)
  {
    $chunks = explode('#', $transaction);
    $start = $chunks[0];
    $middle = $chunks[1];
    if (isset($chunks[2])) {
      $end = $chunks[2];
      $data = explode('/', $end);
      $arrayChunks = array_chunk($data, 2);
      $optinilData = [];
      foreach ($arrayChunks as $arr) {
        $optinilData[$arr[0]] = $arr[1];
      }
    }

    $dateYmd = substr($start, 0, 8);
    $amountBeforFomate = substr($start, 8);
    $amountAfterFormate = $this->getAmountDb($amountBeforFomate);

    // عرفتها عن طريق البحث createFromFormat
    $date = Carbon::createFromFormat('Ymd', $dateYmd)->toDateString();

    return [
      'date' => $date,
      'amount' => $amountAfterFormate,
      'refrance_key' => $middle,
      'notes' => json_encode($optinilData ?? []),
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }
  private function AcmeMethodData($transaction)
  {
    $chunks = explode('//', $transaction);
    $start = $chunks[0];
    $middle = $chunks[1];
    $end = $chunks[2];

    // عرفتها عن طريق البحث createFromFormat
    $date = Carbon::createFromFormat('Ymd', $start)->toDateString();
    $amountAfterFormate = $this->getAmountDb($end);

    return [
      'date' => $date,
      'amount' => $amountAfterFormate,
      'refrance_key' => $middle,
      'notes' => json_encode([]),
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }

  private function PayTechMethodStore($transaction)
  {
    $data = $this->PayTechMethodData($transaction);
    $this->transations[] = $data;
  }
  private function AcmeMethodStore($transaction)
  {
    $data = $this->AcmeMethodData($transaction);
    $this->transations[] = $data;
  }

  //عملتها عشان ال (,) مش بتتقبل في ال db ولازم تبقى  (.) 
  private function getAmountDb($mount)
  {
    return str_replace(',', '.', $mount);
  }

  public function ingestionTransactions()
  {
    $transactions = PendingTransaction::all();
    foreach ($transactions as $transaction) {
      $this->paymentProcessing(json_decode($transaction->data));
      $transaction->delete();
    }
  }

  public function pendingTransactions(array $data)
  {
    PendingTransaction::create([
      'data' => json_encode($data)
    ]);
  }

  private function paymentProcessing(array $transactions)
  {
    foreach ($transactions as $transaction) {
      $type = str_contains($transaction, '#') ? 'PayTech' : 'Acme';
      match ($type) {
        'PayTech' => $this->PayTechMethodStore($transaction),
        'Acme' => $this->AcmeMethodStore($transaction),
      };
    }
    Transaction::insertOrIgnore($this->transations);
  }

}
