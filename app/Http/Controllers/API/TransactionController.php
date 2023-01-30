<?php

namespace App\Http\Controllers\API;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use Midtrans\Snap;
use Midtrans\Config;

class TransactionController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 6);
        $rice_id = $request->input('rice_id');
        $status = $request->input('status');

       
        if($id)
        {
            $transaction = Transaction::with(['rice', 'user'])->find($id);

            if($transaction)
            {
                return ResponseFormatter::success(
                    $transaction,
                    'Data transaksi berhasil diambil'
                );
            }
            else
            {
                return ResponseFormatter::error(
                    null,
                    'Data transaksi tidak ada',
                    404
                );
            }
        }

        $transaction = Transaction::with(['rice', 'user'])
                        ->where('user_id', Auth::user()->id);

        if($rice_id)
            $transaction->where('rice_id', $rice_id);
            
        if($status)
        $transaction->where('status', $status);


        return ResponseFormatter::success(
            $transaction->paginate($limit),
            'Data list transaksi berhasil diambil'
        );
    }
    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        $transaction -> update($request->all());

        return ResponseFormatter::success($transaction, 'Transaksi berhasi diperbarui');
    }
    public function checkout(Request $request)
    {
        $request->validate([
            'rice_id' => 'required',
            'user_id' => 'required',
            'quantity' => 'required',
            'total' => 'required',
            'status' => 'required',
        ]);

        $transaction = Transaction::create([
            'rice_id' => $request->rice_id,
            'user_id' => $request->user_id,
            'quantity' => $request->quantity,
            'status' => $request->status,
            'total' => $request->total,
            'payment_url' => '',
        ]);

        //konfig midtrans
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');

        //Memanggil transaksi yang dibuat
        $transaction = Transaction::with(['rice','user'])->find($transaction->id);
        
        //Membuat Transaksi midtrans
        $midtrans = [
            'transaction_details' => [
                'order_id' => $transaction->id,
                'gross_amount' => (int)$transaction->total,

            ],
            'customers_detail' => [
                'firs_name' => $transaction->user->name,
                'email' => $transaction->user->email,
            ],
            'enable_payment' => ['gopay','bank_transfer'],
            'vtweb' => []
        ];
        //calling midrans
        try {
            //Ambil halaman payment Midtrans
            $paymentUrl = Snap::createTransaction($midtrans)->redirect_url;
            $transaction->payment_url = $paymentUrl;
            $transaction->save();

            //Membalikan data to API
            return ResponseFormatter::success($transaction, 'Transaksi berhasil');
        }
        catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 'Transaksi Gagal');
        }

    }
}
