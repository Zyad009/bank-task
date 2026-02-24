<?php

namespace App\Services;

use function Symfony\Component\String\s;

class BankService
{
  public function genrateTransaction(string $type = 'PayTech', ?int $count = null)
  {
    try {
      $data = match ($type) {
        'PayTech' => $this->PayTechMethod($count),
        'Acme'    => $this->AcmeMethod($count),
      };

      return $data;
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  private function PayTechMethod($count = null)
  {
    try {
      if ($count && $count > 0) {
        $transactions = [];
        for ($i = 0; $i < $count; $i++) {
          $transactions[] = $this->createTextPayTech();
        }
        return $transactions;
      }

      return [$this->createTextPayTech()];
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  private function createTextPayTech()
  {
    try {
      $amount = rand(100, 1000);
      $amountFormatted = number_format($amount, 2, ',', '');
      $date = today()->format('Ymd');
      $rand1 = rand(10000000, 99999999);
      $rand2 = rand(10000000, 99999999);
      $referenceKey = $rand1 . $rand2;
      $notes = 'note/debt payment march/internal_reference/A462JE81';
      $text = $date . $amountFormatted . '#' . $referenceKey . '#' . $notes;

      return $text;
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  private function createTextAcme()
  {
    try {
      $amount = rand(100, 1000);
      $amountFormatted = number_format($amount, 2, ',', '');
      $date = today()->format('Ymd');
      $rand1 = rand(10000000, 99999999);
      $rand2 = rand(10000000, 99999999);
      $referenceKey = $rand1 . $rand2;
      $text = $date . '//' . $referenceKey . '//' . $amountFormatted;

      return $text;
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  private function AcmeMethod($count = null)
  {
    try {
      if ($count && $count > 0) {
        $transactions = [];
        for ($i = 0; $i < $count; $i++) {
          $transactions[] = $this->createTextAcme();
        }
        return $transactions;
      }

      return [$this->createTextAcme()];
    } catch (\Throwable $th) {
      throw $th;
    }
  }
}
