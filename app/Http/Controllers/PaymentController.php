<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Payment result page — browser lands here after checkout.
     * GET /payment/result
     * Query params: status (0 = success), tran_id, apv
     */
    public function result(Request $request)
    {
        $status = $request->input('status');
        $tranId = $request->input('tran_id');
        $amount = $request->input('apv');

        return view('payment.result', compact('status', 'tranId', 'amount'));
    }

    /**
     * Payment cancel page — browser lands here when user cancels.
     * GET /payment/cancel
     */
    public function cancel(Request $request)
    {
        return view('payment.cancel');
    }
}
