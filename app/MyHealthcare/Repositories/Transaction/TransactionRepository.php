<?php

namespace App\MyHealthcare\Repositories\Transaction;

use App\MyHealthcare\Helpers\GenerateCode;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionRepository implements TransactionInterface
{
	/**
	 * @var Transaction
	 */
	private $transaction;

	private $generateCode;

	/**
	 * TransactionRepository constructor.
	 * @param Transaction $transaction
	 */
	public function __construct(Transaction $transaction, GenerateCode $generateCode)
    {
		$this->transaction = $transaction;
		$this->generateCode = $generateCode;
	}

    public function create($paymentType, $amount, $bookingId, $patientId, $paymentStatus = 0, $txnNumber = null, $refNumber = null)
    {
        $transaction = $this->transaction;

        $transaction->payment_type = $paymentType;

        $transaction->patient_id = $patientId;

        $transaction->booking_id = $bookingId;

        // $transaction->payment_code = $this->generateCode->generateCode(
        //     $transaction,
        //     'payment_code',
        //     'PAYID'
        // );

        $transaction->amount = $amount;

        $transaction->payment_status = $paymentStatus;

        $transaction->txn_number = $txnNumber;

        $transaction->ref_number = $refNumber;

        $transaction->save();
        $transaction->fresh();

        return $transaction;
    }

    public function getTransactions($id, $request)
    {
      return $this->transaction->where('patient_id', $id)->orderBy('created_at', 'desc')->paginate(config('api.aggregator_api.items_per_page'));
    }
}
