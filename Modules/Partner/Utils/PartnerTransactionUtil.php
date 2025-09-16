<?php
namespace Modules\Partner\Utils;

use App\Business;
use Modules\Partner\Entities\Partner;
use Modules\Partner\Entities\PartnerTransaction;
use Modules\Partner\Entities\PartnerTransactionPayment;
use Modules\Partner\Entities\PartnerReceipt;
use \Carbon\Carbon;

class PartnerTransactionUtil extends \App\Utils\Util
{
    public function getListPartnerPayments($business_id)
    {
        $query = PartnerTransaction::from("partner_transactions as pt")
            ->join("partners as p", "p.id", "=", "pt.partner_id")
            ->where('pt.business_id', $business_id)
            ->where('pt.type', 'payment')
            ->select(
                'pt.*',
                'p.surname',
                'p.name',
            );
        return $query;
    }

    public function createOrUpdatePaymentLines($transaction, $payments, $business_id = null, $user_id = null, $uf_data = true)
    {
        $edit_ids = [0];
        $new_payments = [];
        $prefix_type = 'partner_payment';

        if (!is_object($transaction)) {
            $transaction = PartnerTransaction::findOrFail($transaction);
        }

        foreach ($payments as $payment) {
            // from_month & to_month
            list($from_month, $to_month) = explode('-', $payment['pay_months']);
            $from_month = \DateTime::createFromFormat('m/Y', trim($from_month));
            $to_month = \DateTime::createFromFormat('m/Y', trim($to_month));

            $from_month = $from_month->format('Y-m-01');
            $to_month = $to_month->format('Y-m-01');

            // amount
            $amount = $uf_data ? $this->num_uf($payment['amount']) : $payment['amount'];
            if ($amount <= 0)
                continue;

            $payment_data = [
                'transaction_id' => $transaction->id,
                'business_id' => $transaction->business_id,
                'service_id' => $payment['service_id'],
                'qty' => $payment['qty'],
                'unit_cost' => $payment['unit_cost'],
                'is_return' => isset($payment['is_return']) ? $payment['is_return'] : 0,
                'amount' => $amount,
                'paid_on' => $transaction['transaction_date'],
                'authorizor' => $payment['authorizor'],
                'from_month' => $from_month,
                'to_month' => $to_month,
                'created_by' => !empty($user_id) ? $user_id : auth()->user()->id,
                'partner_id' => $transaction->partner_id,
                'detail' => $payment['detail'],
            ];

            if (!empty($payment['payment_id'])) {
                $edit_ids[] = $payment['payment_id'];

                $tp = PartnerTransactionPayment::findOrFail($payment['payment_id']);
                $tp->update($payment_data);
            } else {
                $new_payments[] = new PartnerTransactionPayment($payment_data);
            }
        }

        if (!empty($edit_ids)) {
            $transaction->payment_lines()->whereNotIn('id', $edit_ids)->delete();
        }

        if (!empty($new_payments)) {
            $transaction->payment_lines()->saveMany($new_payments);
        }

        return true;
    }

    public function getLastPayment($partner_id, $additional_payment=0)
    {
        try {
            $query = PartnerReceipt::where('partner_id', $partner_id)
              ->where(function($query) {
                $query->whereNull('deleted')->orWhere('deleted', '<>', 1);
              })
              ->where('paid', 1);
            if($additional_payment == 1) $query->where('additional_payment', 1);
            $last_pay_month = $query->max('to_month');
            if (empty($last_pay_month))
                return null;

            $payment = PartnerReceipt::where('partner_id', $partner_id)
              ->where('to_month', $last_pay_month)
              ->where(function($query) {
                $query->whereNull('deleted')->orWhere('deleted', '<>', 1);
              })
              ->first();

            return [
                'month' => Carbon::parse($last_pay_month)->format('m/Y'),
                'amount' => $payment->amount / $payment->months,
                'currency' => $payment->currency,
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function getLastReceipt($partner_id, $additional_payment=0)
    {
        try {
            $query = PartnerReceipt::where('partner_id', $partner_id);
            if($additional_payment == 1) $query->where('additional_payment', 1);

            $last_receipt_month = $query->max('to_month');
            if (empty($last_receipt_month))
                return null;

            $receipt = PartnerReceipt::where('partner_id', $partner_id)->where('to_month', $last_receipt_month)->first();

            return [
                'month' => Carbon::parse($last_receipt_month)->format('m/Y'),
                'amount' => $receipt->amount / $receipt->months,
                'currency' => $receipt->currency,
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getDebt($partner_id)
    {
        try {
            $last_pay_month = PartnerReceipt::where('partner_id', $partner_id)
                ->where('paid', 1)
                ->where(function($query) {
                  $query->whereNull('deleted')->orWhere('deleted', '<>', 1);
                })
                ->max('to_month');

            if (empty($last_pay_month)) {
                $first_charge_month = PartnerReceipt::where('partner_id', $partner_id)
                    ->where('paid', 0)
                    ->where(function($query) {
                      $query->whereNull('deleted')->orWhere('deleted', '<>', 1);
                    })
                    ->min('from_month');

                if (empty($first_charge_month))
                    return null;

                $first_charge_month = Carbon::parse($first_charge_month);
            } else {
                $first_charge_month = Carbon::parse($last_pay_month)->addMonth();
                $last_charge_ym_str = substr($last_pay_month, 0, 7);

                $now = new \DateTime(date('Y-m'));
                $currentYearMonth = $now->format('Y-m'); // e.g., "2025-09"

                if ($last_charge_ym_str == $currentYearMonth)
                    return null;
            }

            $today = new \DateTime(date('Y-m-d'));
            $interval = $first_charge_month->diff($today);
            $debt_months = $interval->m + ($interval->y * 12);

            $partner = Partner::findOrFail($partner_id);
            return [
                'first_month' => $first_charge_month->format('m/Y'),
                'last_month' => Carbon::now()->subMonth()->format('m/Y'),
                'monthly_fee' => $partner->monthly_fee,
                'months' => $debt_months,
                'currency' => $partner->currency,
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function hasReceiptIssued($partner_id, $month, $additional_payment=0)
    {
        $m = $month->format('Y-m-01');
        $query = PartnerReceipt::where('partner_id', $partner_id)
            ->where('from_month', '<=', $m)
            ->where('to_month', '>=', $m);
        if($additional_payment == 1) {
            $query->where('additional_payment', 1);
        }

        $count = $query->count();
        return $count > 0;
    }
}