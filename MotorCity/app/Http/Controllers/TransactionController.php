<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;
use App\Models\Brand;
use App\Models\Transaction;
use App\Models\TransactionRow;
use Log;
class TransactionController extends Controller
{
    public function postAddTransaction(Request $request)
    {
        if(Auth::user()->admin)
        {
            $brandId = $request['brandIdInput'];
        }

        else
            $brandId = Auth::user()->brandId;

        $balanceInput = $request['balanceInput'];
        if(!strcmp($balanceInput,"cash") || !strcmp($balanceInput,"custodyCash") || !strcmp($balanceInput,"cashDollar") || !strcmp($balanceInput,"check") ||!strcmp($balanceInput,"visa"))
        {
            $account = Account::where([['type','=',$balanceInput],['brandId','=',$brandId]])->first();
            if($account === null)
            {
                $brand = Brand::where('id','=',$brandId)->first();
                $name = $brand->name . ":" . $balanceInput;
                $account = new Account();
                $account->init($name, $balanceInput, 0, null, $brandId);
                $account->save();
            }
        }
        else
            $account = Account::where('id','=',$balanceInput)->first();

        //now i have the account or created it. Just create the transaction and increment the account.
        $transaction = new Transaction();
        if(!strcmp($balanceInput,"check"))
            $transaction->init($account->id, Auth::user()->id, $request['typeInput'], $request['valueInput'], $request['dateInput'], $request['fromBankInput'], $request['toBankInput'], $request['noteInput'], $request['clientNameInput'], $brandId);
        else
            $transaction->init($account->id, Auth::user()->id, $request['typeInput'], $request['valueInput'], $request['dateInput'], null, null, $request['noteInput'], $request['clientNameInput'], $brandId);
        
        $transSaved = $transaction->save();
        if($transSaved)
        {
            if(!strcmp($transaction->type,"add"))
                $account->balance = $account->balance + $transaction->value;
            else
            $account->balance = $account->balance - $transaction->value;
            $accountSaved = $account->save();
            if(!$accountSaved)
                App::abort(500, 'Error');
        }
        else
                App::abort(500, 'Error');
    
        return redirect()->back();
    }

    public function getQueryBrandAllTransactions()
    {
        $brands = Brand::all();
        return view('transactions/queryBrandAllTransactions',["transactionsRows"=>[],
                                                            "cashBalance"=>0,
                                                            "cashDollarBalance"=>0,
                                                            "custodyCashBalance"=>0,
                                                            "checKBalance"=>0,
                                                            "visaBalance"=>0,
                                                            "banksBalance"=>0,
                                                            "brands"=>$brands]);
    }

    public function getBrandAllTransactions(Request $request)
    {
        Log::debug('a7a');
        $brands = Brand::all();
        $brandId = 0; //just intialization
        if($request['brandIdInput'] === null) //if a none admin user
            $brandId = Auth::user()->brandId;
        else // it's an admin user. He chose a brand.
            $brandId = $request['brandIdInput']; 
        // Log::debug('brandID = '); Log::debug($brandId);
        $fromDate = $request['fromDateInput'];
        $toDate = $request['toDateInput'];
        $transactions = Transaction::getTransactionAllAccounts($brandId, $fromDate, $toDate);
        
        $brandCashBalanceAtToDate = Transaction::getBrandCurrentBalanceOfAccountTypeAtDate($brandId, 'cash', $toDate);
        $brandCashDollarBalanceAtToDate = Transaction::getBrandCurrentBalanceOfAccountTypeAtDate($brandId, 'cashDollar', $toDate);
        $brandCustodyCashBalanceAtToDate = Transaction::getBrandCurrentBalanceOfAccountTypeAtDate($brandId, 'custodyCash', $toDate);
        $brandCheckBalanceAtToDate = Transaction::getBrandCurrentBalanceOfAccountTypeAtDate($brandId, 'check', $toDate);
        $brandVisaBalanceAtToDate = Transaction::getBrandCurrentBalanceOfAccountTypeAtDate($brandId, 'visa', $toDate);
        $brandBanksBalanceAtToDate = Transaction::getBrandCurrentBanksBalanceAtDate($brandId, $toDate);

        $transactionsRows = Transaction::transactionsToTransactionsRows($transactions);
        // Log::debug($transactionsRows);
        return view('transactions/queryBrandAllTransactions',["transactionsRows"=>$transactionsRows,
                                                            "cashBalance"=>$brandCashBalanceAtToDate,
                                                            "cashDollarBalance"=>$brandCashDollarBalanceAtToDate,
                                                            "custodyCashBalance"=>$brandCustodyCashBalanceAtToDate,
                                                            "checKBalance"=>$brandCheckBalanceAtToDate,
                                                            "visaBalance"=>$brandVisaBalanceAtToDate,
                                                            "banksBalance"=>$brandBanksBalanceAtToDate,
                                                            "brands"=>$brands]);
    }

    public function getQueryAccountTransaction($accountType)
    {
        $brands = Brand::all();
        return view('transactions/queryBrandAccountTransactions',['accountType'=>$accountType, 'transactions'=>[], 'brands'=>$brands]);
    }

    public function getBrandAccountTransaction($accountType, Request $request)
    {
        $brands = Brand::all();
        $brandId = 0; //just intialization
        if($request['brandIdInput'] != null)
            $brandId = $request['brandIdInput'];
        else
            $brandId = Auth::user()->brandId;
        $account = Account::where('brandID',$brandId)->where("type", $accountType)->first();
        if($account === null)
            return view('transactions/queryBrandAccountTransactions',['accountType'=>$accountType,'transactions'=>[], 'brands'=>$brands]);

        $fromDate = $request['fromDateInput'];
        $toDate = $request['toDateInput'];

        $transactions = [];
        $transactions = Transaction::getTransactionOfAccount( $account->id, $fromDate, $toDate);

        return view('transactions/queryBrandAccountTransactions',['accountType'=>$accountType,'transactions'=>$transactions, 'brands'=>$brands]);
    }

    public function getQueryBankAccountTransaction($accountId)
    {
        $transactions = [];
        return view('transactions/queryBankAccountTransactions',['accountId'=>$accountId,'transactions'=>$transactions]);
    }
    public function getBankAccountTransaction($accountId, Request $request)
    {
        $fromDate = $request['fromDateInput'];
        $toDate = $request['toDateInput'];

        $transactions = [];
        $transactions = Transaction::getTransactionOfAccount( $accountId, $fromDate, $toDate);
        return view('transactions/queryBankAccountTransactions',['accountId'=>$accountId,'transactions'=>$transactions]);
    }
}
