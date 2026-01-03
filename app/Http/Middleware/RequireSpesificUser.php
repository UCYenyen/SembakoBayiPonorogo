<?php

namespace App\Http\Middleware;

use App\Models\Transaction;
use App\Models\Testimony;
use App\Models\TransactionItem;
use App\Models\Address; // Tambahkan ini
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireSpesificUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        if ($addressParam = $request->route('address')) {
            $address = $addressParam instanceof Address 
                ? $addressParam 
                : Address::find($addressParam);

            if ($address && auth()->id() !== $address->user_id) {
                return redirect('/unauthorized');
            }
        }

        $testimonyParam = $request->route('testimony');
        if ($testimonyParam) {
            $testimony = $testimonyParam instanceof Testimony 
                ? $testimonyParam 
                : Testimony::find($testimonyParam);

            if ($testimony && $testimony->transactionItem && $testimony->transactionItem->transaction) {
                if (auth()->id() !== $testimony->transactionItem->transaction->user_id) {
                    return redirect('/unauthorized');
                }
            }
        }

        if ($transactionItemParam = $request->route('transactionItem')) {
            $item = $transactionItemParam instanceof TransactionItem
                ? $transactionItemParam
                : TransactionItem::find($transactionItemParam);

            if ($item && $item->transaction) {
                if (auth()->id() !== $item->transaction->user_id) {
                    return redirect('/unauthorized');
                }
            }
        }

        if ($transactionParam = $request->route('transaction')) {
            $transaction = $transactionParam instanceof Transaction
                ? $transactionParam
                : Transaction::find($transactionParam);

            if ($transaction && auth()->id() !== $transaction->user_id) {
                return redirect('/unauthorized');
            }
        }

        return $next($request);
    }
}