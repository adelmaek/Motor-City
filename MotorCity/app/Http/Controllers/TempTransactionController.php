<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TempTransaction;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Brand;
use Carbon\Carbon;
use Log;
use DB;
use App;

class TempTransactionController extends Controller
{
    public function getTempTransactions()
    {
        $todayDate = Carbon::today('Egypt')->toDateString();
        $brands = Brand::all();
        $bankAccounts = Account::getBankAccounts();

        $transactions = [];
        $transactions = TempTransaction::where('confirmed', 0)->whereYear('date', Carbon::now('Egypt')->year)->whereMonth('date', Carbon::now('Egypt')->month)->orderBy('date','Desc')->orderBy('id', 'Desc')->get();
        return view('transactions.tempTransactions',['transactions'=>$transactions, 'brands'=>$brands, "today"=>$todayDate, "bankAccounts"=>$bankAccounts]);
    }

    public function postTempTransaction(Request $request)
    {
        $transaction = new TempTransaction();
        $transaction->init($request['dateInput'], $request['bankInput'], $request['valueInput'], $request['commentInput']);
        $transaction->save();

        return redirect()->back();
    }

    public function getDeleteTempTransaction($tranactionId)
    {
        TempTransaction::where('id', $tranactionId)->first()->delete();

        return redirect()->back();
    }

    public function postConfirmTempTransaction($transactionId, Request $request)
    {
        Log::debug($request);
        $tempTransaction = TempTransaction::where('id', $transactionId)->first();
        $tempTransaction->confirmed = 1;

        $bankTransaction = new Transaction();
        $bankTransaction->init($tempTransaction->accountId, Auth::user()->id, "add", $tempTransaction->value, $tempTransaction->date, null, null, null, null, null ,null,null,$request['descriptionInput'], $request['clientInput'], $request['brandInput'], 0);

        $bankAccount = Account::where('id', $tempTransaction->accountId)->first();
        $bankAccount->balance = $bankAccount->balance + $bankTransaction->value;

        DB::transaction(function () use($tempTransaction, $bankTransaction, $bankAccount){
            $tempTransaction->save();
            $bankTransaction->save();
            $bankAccount->save();
        }, 5);

        return redirect()->back();
    }
}
