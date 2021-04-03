<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;
use App\Models\Brand;
use App\Models\Transaction;
use App\Models\Bank;
use App\Models\TransactionRow;
use Carbon\Carbon;
use Log;
use DB;
use App;
class TransactionController extends Controller
{
    public function postAddTransaction(Request $request)
    {
        DB::transaction(function () use($request){
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
            $description = $request['noteInput'];
            if(!strcmp($balanceInput,"cash") || !strcmp($balanceInput,"custodyCash") || !strcmp($balanceInput,"cashDollar") || !strcmp($balanceInput,"check"))
            {
                $account = Account::where([['type','=',$balanceInput],['brandId','=',$brandId]])->lockForUpdate()->first();
                if($account === null)
                {
                    $brand = Brand::where('id','=',$brandId)->first();
                    $name = $brand->name . ":" . $balanceInput;
                    $account = new Account();
                    $account->init($name, $balanceInput, 0, null, $brandId);
                    $account->save();
                }
                if(!strcmp($balanceInput,"cash") && $request["cashWithdrawalReason"] != null)
                    $description = " (" . $request["cashWithdrawalReason"]  . ") " . $description;
            }
            elseif(!strcmp($balanceInput,"bankToBank"))
            {
                $fromAccount = Account::where('id', $request['fromBank'])->lockForUpdate()->first();
                $toAccount = Account::where('id', $request['toBank'])->lockForUpdate()->first();
            }
            elseif(!strcmp($balanceInput,"banks"))
            {
                $account = Account::where('id','=',$request['bankAccountId'])->lockForUpdate()->first();
                $description = " (تحويل بنك - " .  Bank::where('id', $account->bankID)->first()->name . ") " . $description;
            }
            elseif(!strcmp($balanceInput,"pos"))
            {
                $account = Account::where('id','=',$request['posAccountId'])->lockForUpdate()->first();
            }
            else
                $account = Account::where('id','=',$balanceInput)->lockForUpdate()->first();

            //now i have the account or created it. Just create the transaction and increment the account.
            $transaction=null;
            $fromTransaction=null;
            $toTransaction=null;
            if(!strcmp($balanceInput,"bankToBank"))
            {
                $fromTransaction = new Transaction();
                $toTransaction = new Transaction();
            }
            else //Bank transaction or POS transaction
                $transaction = new Transaction();

        
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
                $transaction->init($account->id, Auth::user()->id, $request['typeInput'], $request['valueInput'], $request['dateInput'], $request['checkIsFromBankInput'], $request['checkNumberInput'], $request['checkValidityDateInput'], false,false,null,null,$description, $request['clientNameInput'], $brandId);
            elseif(!strcmp($balanceInput,"bankToBank"))
            {
                $fromTransaction->init($fromAccount->id, Auth::user()->id, "sub", $request['valueInput'], $request['dateInput'], null, null,null ,null,null,null,null,$description, $request['clientNameInput'], $brandId);
                $toTransaction->init($toAccount->id, Auth::user()->id, "add", $request['valueInput'], $request['dateInput'], null, null,null ,null,null,null,null,$description, $request['clientNameInput'], $brandId);
            }
            else
                $transaction->init($account->id, Auth::user()->id, $request['typeInput'], $request['valueInput'], $request['dateInput'], null, null,null ,null,null,null,null,$description, $request['clientNameInput'], $brandId);
            
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
        $today = Carbon::today('Egypt')->toDateString();
        return view('transactions.queryBrandAllTransactions',["transactionsRows"=>[],
                                                            "cashBalance"=>0,
                                                            "cashDollarBalance"=>0,
                                                            "custodyCashBalance"=>0,
                                                            "checKBalance"=>0,
                                                            "visaBalance"=>0,
                                                            "banksBalance"=>0,
                                                            "brands"=>$brands,
                                                            "today"=>$today]);
    }

    public function getBrandAllTransactions(Request $request)
    {
        $today = Carbon::today('Egypt')->toDateString();
        $brands = Brand::all();
        $brandId = 0; //just intialization
        if($request['brandIdInput'] === null) //if a none admin user
            $brandId = array(Auth::user()->brandId);
        else // it's an admin user. He chose a brand.
        {
            if(!strcmp('all', $request['brandIdInput']))
                $brandId = Brand::all('id');
            else
                $brandId = Brand::where('id', $request['brandIdInput'])->get('id');
        }
        // Log::debug('brandID = '); Log::debug($brandId);
        $fromDate = $request['fromDateInput'];
        $toDate = $request['toDateInput'];
        $transactions = Transaction::getTransactionAllAccounts($brandId, $fromDate, $toDate);
        
        $arrayOfTransactionRowsAndTotalRow = Transaction::transactionsToTransactionsRows($transactions);
        $transactionsRows = $arrayOfTransactionRowsAndTotalRow[0];
        $totals = $arrayOfTransactionRowsAndTotalRow[1];

        return view('transactions.queryBrandAllTransactions',["transactionsRows"=>$transactionsRows,
                                                            "cashBalance"=>$totals->cash,
                                                            "cashDollarBalance"=>$totals->cashDollar,
                                                            "custodyCashBalance"=>$totals->custodyCash,
                                                            "checKBalance"=>$totals->check,
                                                            "visaBalance"=>$totals->visa,
                                                            "banksBalance"=>$totals->banks,
                                                            "brands"=>$brands,
                                                            "today"=>$today]);
    }

    public function getQueryAccountTransaction($accountType)
    {
        $yesterday = Carbon::yesterday()->toDateString();
        $todayDate = Carbon::today('Egypt')->toDateString();
        $brands = Brand::all();
        $bankAccounts = Account::getBankAccounts();
    
        //getting the current month all brands transaction for admin or only brand trasnactions for non admin user
        $transactions = [];
        if(!strcmp("posCommission",$accountType))
        {
            $posAccount = Account::where('type', $accountType)->first();
            if($posAccount)
                $transactions = Transaction::getCurrentMonthTransactions($posAccount->id);

        }
        else
        {
            if(Auth::user()->admin)
                $brandsIds = Brand::all('id');
            else
                $brandsIds = Brand::where('id',Auth::user()->brandId)->get('id');

            $accountsIds = Account::whereIn('brandID', $brandsIds)->where('type', $accountType)->get('id');
            $transactions = Transaction::whereIn('accountId', $accountsIds)->limit(1000)->orderBy('date','Desc')->orderBy('id', 'Desc')->get();
        }     
        return view('transactions.queryBrandAccountTransactions',['accountType'=>$accountType, 'transactions'=>$transactions, 'brands'=>$brands, "todayDate"=>$todayDate, "bankAccounts"=>$bankAccounts,'yesterday'=>$yesterday]);
    }

    public function getBrandAccountTransaction($accountType, Request $request)
    {
        $yesterday = Carbon::yesterday()->toDateString();
        $bankAccounts = Account::getBankAccounts();
        $todayDate = Carbon::today('Egypt')->toDateString();

        $brands = Brand::all();
        $brandId = 0; //just intialization
        if($request['brandIdInput'] != null)
        {
            if(!strcmp('all', $request['brandIdInput']))
                $brandId = Brand::all('id');
            else
                $brandId = Brand::where('id', $request['brandIdInput'])->get('id');
        }
        else
            $brandId = Brand::where('id', Auth::user()->brandId)->get('id');

        // Log::debug($brandId);
        
        $accountIds = null;
        if(!strcmp("posCommission", $accountType))
            $accountIds = Account::where("type", $accountType)->get('id');
        else
            $accountIds = Account::whereIn('brandID',$brandId)->where("type", $accountType)->get('id');

        
        if($accountIds === null || $accountIds->isEmpty())
            return view('transactions.queryBrandAccountTransactions',['accountType'=>$accountType,'transactions'=>[], 'brands'=>$brands, "todayDate"=>$todayDate, "bankAccounts"=>$bankAccounts]);

        $fromDate = $request['fromDateInput'];
        $toDate = $request['toDateInput'];
        
        $transactions = [];
        $transactions = Transaction::getTransactionOfAccounts( $accountIds,$brandId, $fromDate, $toDate);

        return view('transactions.queryBrandAccountTransactions',['accountType'=>$accountType,'transactions'=>$transactions, 'brands'=>$brands, "todayDate"=>$todayDate, "bankAccounts"=>$bankAccounts, 'yesterday'=>$yesterday]);
    }

    public function getQueryBankAccountTransaction($accountId)
    {
        $yesterday = Carbon::yesterday()->toDateString();
        $today = Carbon::today('Egypt')->toDateString();
        $brands = Brand::all();
        $transactions = Transaction::getCurrentMonthTransactions($accountId);
        return view('transactions.queryBankAccountTransactions',['brands'=>$brands,'accountId'=>$accountId,'transactions'=>$transactions, 'yesterday'=>$yesterday, 'today'=>$today]);
    }
    public function getBankAccountTransaction($accountId, Request $request)
    {
        $fromDate = $request['fromDateInput'];
        $toDate = $request['toDateInput'];
        $yesterday = Carbon::yesterday()->toDateString();
        $today = Carbon::today('Egypt')->toDateString();
        $brands = Brand::all();
        $brandId = 0; //just intialization
        if($request['brandIdInput'] != null)
        {
            if(!strcmp('all', $request['brandIdInput']))
                $brandId = Brand::all('id');
            else
                $brandId = Brand::where('id', $request['brandIdInput'])->get('id');
        }
        else
            $brandId = Brand::where('id', Auth::user()->brandId)->get('id');
        // Log::debug($brandId);
        $transactions = [];
        $transactions = Transaction::getTransactionOfAccount($accountId, $brandId, $fromDate, $toDate);
        return view('transactions.queryBankAccountTransactions',['brands'=>$brands,'accountId'=>$accountId,'transactions'=>$transactions,'yesterday'=>$yesterday, 'today'=>$today]);
    }

    public function getDeleteTransaction($transactionId)
    {
        DB::transaction(function () use($transactionId){
            $transaction = Transaction::where('id', $transactionId)->lockForUpdate()->first();
            $account = Account::where('id',$transaction->accountId)->lockForUpdate()->first();
            $newBalance = $account->balance;
            if(!strcmp($transaction->type,"add"))
                $newBalance = $account->balance - $transaction->value;
            else
                $newBalance = $account->balance + $transaction->value;
        
            $deletionStatus = $transaction->deleteTransaction();
            if($deletionStatus)
            {
                $account->balance = $newBalance;
                $account->save();
            }
        }, 5);
        return redirect()->back();
    }

    public function postSettleCheck(Request $request,$transactionId)
    {
        $transaction = Transaction::where('id', $transactionId)->first();
        // Transaction::settleCheck($transaction);
        // Log::debug($request);
        // Log::debug($transactionId);
        $transaction->settled = true;
        $transaction->checkSettlingDate = $request['settlingDateInput'];
        $transaction->checKToBankId = $request['settlingBankInput'];
        $transaction->save();

        return redirect()->back();
    }

    public function getConfirmCheckSettling($transactionId)
    {
        DB::transaction(function () use($transactionId) {
            $transaction = Transaction::where('id', $transactionId)->first();
            $transaction->confirmSettling = true;
            
            $bankTransaction = new Transaction();
            
            $toBankAccount = Account::where('id', $transaction->checKToBankId)->lockForUpdate()->first();

            $toBankAccount->balance = $toBankAccount->balance + $transaction->value;
            
            $description = "Cashing check number" .": ". $transaction->checkNumber;

            $checksAccount = Account::where('id', $transaction->accountId)->lockForUpdate()->first();
            $checksAccount->balance = $checksAccount->balance - $transaction->value;

        
            $transaction->save();
            $toBankAccount->save();
            $bankTransaction->init($transaction->checKToBankId, Auth::user()->id, "add", $transaction->value, $transaction->date, null, null, null, null, null ,null,null,$description, $transaction->clientName, $transaction->brandId, 1);
            $bankTransaction->save();
            $checksAccount->save();
        }, 5);

        return redirect()->back();
    }
    public function postEditDescription(Request $request, $transactionId)
    {
        $transaction = Transaction::where('id', $transactionId)->first();
        $transaction->description = $request['editInput'];
        $transaction->save();
        return redirect()->back();
    }
    public function postEditClientName(Request $request, $transactionId)
    {
        $transaction = Transaction::where('id', $transactionId)->first();
        $transaction->clientName = $request['editInput'];
        $transaction->save();
        return redirect()->back();
    }

    public function getQueryPosAccountTransaction($accountId)
    {
        $yesterday = Carbon::yesterday()->toDateString();
        $today = Carbon::today('Egypt')->toDateString();
        $brands = Brand::all();
        $bankAccounts = Account::getBankAccounts();
        $transactions = Transaction::getCurrentMonthTransactions($accountId);
        return view('transactions.queryPosAccountTransactions',['bankAccounts'=>$bankAccounts,'brands'=>$brands,'accountId'=>$accountId,'transactions'=>$transactions, 'yesterday'=>$yesterday, 'today'=>$today]);
    }

    public function postQueryPosAccountTransaction(Request $request, $accountId)
    {
        // Log::info('postQueryPosAccountTransaction',['request'=>$request,'accountId'=>$accountId]);
        $fromDate = $request['fromDateInput'];
        $toDate = $request['toDateInput'];
        $yesterday = Carbon::yesterday()->toDateString();
        $today = Carbon::today('Egypt')->toDateString();
        $brands = Brand::all();
        $bankAccounts = Account::getBankAccounts();

        $account = Account::where('id', $accountId)->first();
        $brandId = 0;
        if($request['brandIdInput'] != null)
        {
            if(!strcmp('all', $request['brandIdInput']))
                $brandId = Brand::all('id');
            else
                $brandId = Brand::where('id', $request['brandIdInput'])->get('id');
        }
        else
            $brandId = Brand::where('id', Auth::user()->brandId)->get('id');

        $transactions = [];
        $transactions = Transaction::getTransactionOfAccount($accountId, $brandId, $fromDate, $toDate);
        
        return view('transactions.queryPosAccountTransactions',['bankAccounts'=>$bankAccounts,'brands'=>$brands,'accountId'=>$accountId,'transactions'=>$transactions, 'yesterday'=>$yesterday, 'today'=>$today]);
    }

    public function postSettlePosTransactions(Request $request)
    {
        if($request['settled'] === null)
            return redirect()->back();
        DB::transaction(function () use($request) {
            $totalValue = 0;
            $transaction = null;
            foreach($request['settled'] as $transId)
            {
                $transaction = Transaction::where('id',$transId)->first();
                $transaction->settled = true;
                $transactionSaved = $transaction->save();
                if($transactionSaved)
                {
                    if(!strcmp($transaction->type,"add"))
                        $totalValue += $transaction->value;
                    else
                        $totalValue -= $transaction->value;
                }
            }

            if($totalValue != 0 && $transaction != null)
            {
                $settlingTransaction = new Transaction();
                $today = Carbon::today('Egypt')->toDateString();
                $account = Account::where('id', $transaction->accountId)->lockForUpdate()->first();
                $account->balance -= $totalValue;
                $description = "A settling transaction";
                $clientName = Auth::user()->name;
                
                $settlingTransaction->init($account->id, Auth::user()->id, "sub", $totalValue, $today, null, null, null, true, null ,null,null,$description, $clientName, $transaction->brandId, 1);
                $settlingTransaction->save();
                $account->save();
                
            }
        }, 5);
        return redirect()->back();
    }

    public function postConfirmSettlingPos(Request $request, $transactionId)
    {
        DB::transaction(function () use($request, $transactionId) {
            $transaction = Transaction::where('id', $transactionId)->first();

            $brandId = $transaction->brandId;
            $balanceInput = "posCommission";
            $commissionAccount = Account::where([['type','=',$balanceInput]])->lockForUpdate()->first();
            if($commissionAccount === null)
            {
                // $brand = Brand::where('id','=',$brandId)->first();
                // $name = $brand->name . ":" . $balanceInput;
                $name = "POS Commission";
                $commissionAccount = new Account();
                // $commissionAccount->init($name, $balanceInput, 0, null, $brandId);
                $commissionAccount->init($name, $balanceInput, 0, null, null);
                $commissionAccount->save();
            }

            $posAccount = Account::where('id', $transaction->accountId)->lockForUpdate()->first();

            $bankValue = $request["valueInput"];
            // $bankId = $request["bankIdInput"];
            $bankAccount = Account::where('id', Account::where('id', $transaction->accountId)->first()->bankAccountId)->lockForUpdate()->first();
            $commissionValue = $transaction->value - $bankValue;


            $description = "Settling POS " .  $posAccount->name;

            $bankTransaction = new Transaction();
            $commissionTransaction = new Transaction();
            $transaction->confirmSettling = true;
            $bankAccount->balance += $bankValue;
            $commissionAccount->balance += $commissionValue;

            $today = Carbon::today('Egypt')->toDateString();



        
            $bankTransaction->init($bankAccount->id, Auth::user()->id, "add", $bankValue, $today, null, null, null, null, null ,null,null,$description, Auth::user()->name, $brandId, 1);
            $commissionTransaction->init($commissionAccount->id, Auth::user()->id, "add", $commissionValue, $today, null, null, null, null, null ,null,null,$description, Auth::user()->name, $brandId, 1);
            $bankTransaction->save();
            $commissionTransaction->save();
            $transaction->save();
            $commissionAccount->save();
            $bankAccount->save();
        }, 5);

        return redirect()->back();
    }
    public function getSearchTransactions(Request $request)
    {
        $transactions = Transaction::where('clientName','like', '%'.$request["searchInput"].'%')->orWhere('description','like','%'.$request["searchInput"].'%')->orWhere('value', $request["searchInput"])->get();

        return view('transactions.searchResults',["transactions"=>$transactions]);
    }
}
