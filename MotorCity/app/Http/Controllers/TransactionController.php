<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;
use App\Models\Brand;
use App\Models\Transaction;
use App\Models\TransactionRow;
use Carbon\Carbon;
use Log;
use DB;
use App;
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
        $account=null;
        $toAccount=null;
        $fromAccount=null;
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
        elseif(!strcmp($balanceInput,"bankToBank"))
        {
            $fromAccount = Account::where('id', $request['fromBank'])->first();
            $toAccount = Account::where('id', $request['toBank'])->first();
        }
        else
            $account = Account::where('id','=',$balanceInput)->first();

        //now i have the account or created it. Just create the transaction and increment the account.
        $transaction=null;
        $fromTransaction=null;
        $toTransaction=null;
        if(!strcmp($balanceInput,"bankToBank"))
        {
            $fromTransaction = new Transaction();
            $toTransaction = new Transaction();
        }
        else
            $transaction = new Transaction();

        DB::transaction(function () use($balanceInput, $transaction, $account, $request,$brandId,$fromTransaction,$toTransaction,$fromAccount,$toAccount){
            if(!strcmp($balanceInput,"bankToBank"))
            {
                if(($fromTransaction===null || $toTransaction===null || $fromAccount===null ||$toAccount===null))
                {
                    Log::error('Null attributes for bank to bank transaction.');
                    return;
                }
            }                
            else
            {
                if($account === null || $transaction === null)
                {
                    Log::error('Null attributes for transaction.');
                    return;
                }               
            }
            
            if(!strcmp($balanceInput,"check"))
                $transaction->init($account->id, Auth::user()->id, $request['typeInput'], $request['valueInput'], $request['dateInput'], $request['checkIsFromBankInput'], $request['checkNumberInput'], $request['checkValidityDateInput'], false,false,null,null,$request['noteInput'], $request['clientNameInput'], $brandId);
            elseif(!strcmp($balanceInput,"bankToBank"))
            {
                $fromTransaction->init($fromAccount->id, Auth::user()->id, "sub", $request['valueInput'], $request['dateInput'], null, null,null ,null,null,null,null,$request['noteInput'], $request['clientNameInput'], $brandId);
                $toTransaction->init($toAccount->id, Auth::user()->id, "add", $request['valueInput'], $request['dateInput'], null, null,null ,null,null,null,null,$request['noteInput'], $request['clientNameInput'], $brandId);
            }
            else
                $transaction->init($account->id, Auth::user()->id, $request['typeInput'], $request['valueInput'], $request['dateInput'], null, null,null ,null,null,null,null,$request['noteInput'], $request['clientNameInput'], $brandId);
            
            $transSaved=0;
            $fromTransSaved=0;
            $toTransSaved=0;

            if(!strcmp($balanceInput,"bankToBank"))
            {
                $fromTransSaved = $fromTransaction->save();
                $toTransSaved = $toTransaction->save();
            }
            else
                $transSaved = $transaction->save();

            if($fromTransSaved && $toTransSaved)
            {
                $fromAccount->balance = $fromAccount->balance - $fromTransaction->value;
                $toAccount->balance = $toAccount->balance + $toTransaction->value;
                $fromAccount->save();
                $toAccount->save();
            }
            elseif($transSaved)
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
        }, 5);
    
        return redirect()->back();
    }

    public function getQueryBrandAllTransactions()
    {
        $brands = Brand::all();
        return view('transactions.queryBrandAllTransactions',["transactionsRows"=>[],
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
        return view('transactions.queryBrandAllTransactions',["transactionsRows"=>$transactionsRows,
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
        $yesterday = Carbon::yesterday()->toDateString();
        $todayDate = Carbon::today()->toDateString();
        $brands = Brand::all();
        $bankAccounts = Account::getBankAccounts();
        return view('transactions.queryBrandAccountTransactions',['accountType'=>$accountType, 'transactions'=>[], 'brands'=>$brands, "todayDate"=>$todayDate, "bankAccounts"=>$bankAccounts,'yesterday'=>$yesterday]);
    }

    public function getBrandAccountTransaction($accountType, Request $request)
    {
        $yesterday = Carbon::yesterday()->toDateString();
        $bankAccounts = Account::getBankAccounts();
        $todayDate = Carbon::today()->toDateString();

        $brands = Brand::all();
        $brandId = 0; //just intialization
        if($request['brandIdInput'] != null)
            $brandId = $request['brandIdInput'];
        else
            $brandId = Auth::user()->brandId;
        $account = Account::where('brandID',$brandId)->where("type", $accountType)->first();
        if($account === null)
            return view('transactions.queryBrandAccountTransactions',['accountType'=>$accountType,'transactions'=>[], 'brands'=>$brands, "todayDate"=>$todayDate, "bankAccounts"=>$bankAccounts]);

        $fromDate = $request['fromDateInput'];
        $toDate = $request['toDateInput'];
        
        $transactions = [];
        $transactions = Transaction::getTransactionOfAccount( $account->id, $fromDate, $toDate);

        

        return view('transactions.queryBrandAccountTransactions',['accountType'=>$accountType,'transactions'=>$transactions, 'brands'=>$brands, "todayDate"=>$todayDate, "bankAccounts"=>$bankAccounts, 'yesterday'=>$yesterday]);
    }

    public function getQueryBankAccountTransaction($accountId)
    {
        $yesterday = Carbon::yesterday()->toDateString();
        $today = Carbon::yesterday()->toDateString();
        $transactions = [];
        return view('transactions.queryBankAccountTransactions',['accountId'=>$accountId,'transactions'=>$transactions, 'yesterday'=>$yesterday, 'today'=>$today]);
    }
    public function getBankAccountTransaction($accountId, Request $request)
    {
        $fromDate = $request['fromDateInput'];
        $toDate = $request['toDateInput'];
        $yesterday = Carbon::yesterday()->toDateString();
        $today = Carbon::yesterday()->toDateString();
        $transactions = [];
        $transactions = Transaction::getTransactionOfAccount( $accountId, $fromDate, $toDate);
        return view('transactions.queryBankAccountTransactions',['accountId'=>$accountId,'transactions'=>$transactions,'yesterday'=>$yesterday, 'today'=>$today]);
    }

    public function getDeleteTransaction($transactionId)
    {
        $transaction = Transaction::where('id', $transactionId)->first();
        $account = Account::where('id',$transaction->accountId)->first();
        DB::transaction(function () use($transaction, $account){
            $deletionStatus = $transaction->deleteTransaction();
            if($deletionStatus)
            {
                $account->balance = $account->balance - $transaction->value;
                $account->save();
            }
        }, 5);
        return redirect()->back();
    }

    public function postSettleCheck(Request $request,$transactionId)
    {
        $transaction = Transaction::where('id', $transactionId)->first();
        // Transaction::settleCheck($transaction);
        Log::debug($request);
        Log::debug($transactionId);
        $transaction->settled = true;
        $transaction->checkSettlingDate = $request['settlingDateInput'];
        $transaction->checKToBankId = $request['settlingBankInput'];
        $transaction->save();

        return redirect()->back();
    }

    public function getConfirmCheckSettling($transactionId)
    {
        $transaction = Transaction::where('id', $transactionId)->first();
        $transaction->confirmSettling = true;
        
        $bankTransaction = new Transaction();
        
        $toBankAccount = Account::where('id', $transaction->checKToBankId)->first();

        $toBankAccount->balance = $toBankAccount->balance + $transaction->value;
        
        $description = "Cashing check number" .": ". $transaction->checkNumber;

        $checksAccount = Account::where('id', $transaction->accountId)->first();
        $checksAccount->balance = $checksAccount->balance - $transaction->value;

        DB::transaction(function () use($transaction, $toBankAccount, $bankTransaction, $description,$checksAccount) {
            $transaction->save();
            $toBankAccount->save();
            $bankTransaction->init($transaction->checKToBankId, Auth::user()->id, "add", $transaction->value, $transaction->date, null, null, null, null, null ,null,null,$description, $transaction->clientName, $transaction->brandId);
            $bankTransaction->save();
            $checksAccount->save();
        }, 5);

        return redirect()->back();
    }
}
