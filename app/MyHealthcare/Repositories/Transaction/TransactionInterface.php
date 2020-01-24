<?php

namespace App\MyHealthcare\Repositories\Transaction;

interface TransactionInterface
{
    public function create($paymentType, $amount, $bookingId, $patientId, $paymentStatus = 0, $txnNumber = null, $refNumber = null);
    
    public function getTransactions($id, $request);
}
