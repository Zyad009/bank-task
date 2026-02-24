<?php

namespace App\Services;

use SimpleXMLElement;

use function Symfony\Component\String\s;

class GenerateXml
{

  public function generate(array $data)
  {
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><PaymentRequestMessage/>');
    $TransferInfo = $xml->addChild('TransferInfo');
    $TransferInfo->addChild('Reference', $data['reference']);
    $TransferInfo->addChild('Date', $data['date']);
    $TransferInfo->addChild('Amount', $data['amount']);
    $TransferInfo->addChild('Currency', $data['currency']);

    $SenderInfo = $xml->addChild('SenderInfo');
    $SenderInfo->addChild('AccountNumber', $data['sender_account']);

    $ReceiverInfo = $xml->addChild('ReceiverInfo');
    $ReceiverInfo->addChild('BankCode', $data['receiver_bank_code']);
    $ReceiverInfo->addChild('AccountNumber', $data['receiver_account']);
    $ReceiverInfo->addChild('BeneficiaryName', $data['beneficiary_name']);

    $notes = $data['notes'] ?? [];
    if (!empty($notes)) {
      $notesNode = $xml->addChild('Notes');
      foreach ($notes as $note) {
        $notesNode->addChild('Note', $note);
      }
    }

    if (isset($data['payment_type']) && (int)$data['payment_type'] !== 99) {
      $xml->addChild('PaymentType', (string)$data['payment_type']);
    }

    if (isset($data['charge_details']) && strtoupper($data['charge_details']) !== 'SHA') {
      $xml->addChild('ChargeDetails', $data['charge_details']);
    }

    return $xml->asXML();
  }
}
