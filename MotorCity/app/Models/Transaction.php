<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TransactionRow;
use Log;
use DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'brandId',
        'accountId',
        'userId',
        'type',
        'value',
        'date',
        'fromBankId',
        'checkNumber',
        'checkValidityDate',
        'description',
        'clientName',
        'currentBalance'
    ];
    public function init($accountId=null, $userId=null, $type=null, $value=null, $date=null, $fromBankName=null, $checkNumber=null,$validityDate=null, $settled=null, $confirmSettling =null,$checKToBankId,$checkSettlingDate, $description=null, $clientName=null, $brandId)
    {
        $this->brandId = $brandId;
        $this->accountId = $accountId;
        $this->userId = $userId;
        $this->type = $type;
        $this->value = $value;
        $this->date = $date;
        $this->fromBankName = $fromBankName;
        $this->checkNumber = $checkNumber;
        $this->checkValidityDate = $validityDate;
        $this->settled = $settled;
        $this->confirmSettling = $confirmSettling;
        $this->description = $description;
        $this->clientName = $clientName;       
        $this->checKToBankId = $checKToBankId;
        $this->checkSettlingDate = $checkSettlingDate;
       
        $this->updateCurrentBalanceOnAddition();

    }

    private function updateCurrentBalanceOnAddition()
    {
        $prevTransaction = Transaction::where( [['accountId','=',$this->accountId]])->whereDate('date','<=',$this->date)->orderBy('date','Desc')->first();
        if(!empty($prevTransaction))
            $prevTransaction = Transaction::where( [['accountId','=',$this->accountId]])->whereDate('date','=',$prevTransaction->date)->orderBy('id','Desc')->first();
                                                     
        $followingTransactions = Transaction::where([['accountId','=',$this->accountId]])->whereDate('date','>',$this->date)->orderBy('date','Asc')->get();
                                                      
        if(!empty($prevTransaction))
        {
            if(!strcmp($this->type,"add"))
                $currentBalanceInput = $prevTransaction->currentBalance +  $this->value;
            else
                $currentBalanceInput = $prevTransaction->currentBalance - $this->value;
        }
        else
        {
            $account = Account::where('id', $this->accountId)->first();

            if(!strcmp($this->type,"add"))
                $currentBalanceInput = $account->initialBalance + $this->value;
            else
                $currentBalanceInput = $account->initialBalance - $this->value;
        }

        $this->currentBalance = $currentBalanceInput;

        $accumulatedBalance = $currentBalanceInput;

        foreach($followingTransactions as $trans)
        {            
            if(!strcmp($trans->type,"add"))
                $accumulatedBalance = $accumulatedBalance + $trans->value;
            else
                $accumulatedBalance = $accumulatedBalance - $trans->value;
            
            $trans->currentBalance = $accumulatedBalance;
            $trans->save();
        }
    }

    public static function getTransactionAllAccounts($brandId, $fromDate=null, $toDate=null)
    {
        $transactions = [];
      
        if($fromDate === null && $toDate === null)
            $transactions = Transaction::where('brandId', $brandId)->orderBy('date','Asc')->get();
        else if($fromDate != null && $toDate === null)
            $transactions = Transaction::where('brandId', $brandId)->whereDate('date','>=',$fromDate)->orderBy('date','Asc')->get();
        else if ($fromDate === null && $toDate != null)
            $transactions = Transaction::where('brandId', $brandId)->whereDate('date','<=',$toDate)->orderBy('date','Asc')->get();
        else
            $transactions = Transaction::where('brandId', $brandId)->whereDate('date','>=',$fromDate)->whereDate('date','<=', $toDate)->orderBy('date','Asc')->get();
        
        return $transactions;
    }

    // public static function getTransactionOfAccount ( $accountId, $brandId, $fromDate=null, $toDate=null)
    // {
    //     $transactions = [];
    //     // Log::info('getTransactionOfAccount', ['accountId' => $accountId, "brandId"=>$brandId,"fromDate"=>$fromDate,"toDate"=>$toDate]);
    //     if($fromDate === null && $toDate === null)
    //         $transactions = Transaction::where([['accountId',$accountId],['brandId',$brandId]])->orderBy('date','Asc')->get();
    //     else if($fromDate != null && $toDate === null)
    //         $transactions = Transaction::where([['accountId',$accountId],['brandId',$brandId]])->whereDate('date','>=',$fromDate)->orderBy('date','Asc')->get();
    //     else if ($fromDate === null && $toDate != null)
    //         $transactions = Transaction::where([['accountId',$accountId],['brandId',$brandId]])->whereDate('date','<=',$toDate)->orderBy('date','Asc')->get();
    //     else
    //         $transactions = Transaction::where([['accountId',$accountId],['brandId',$brandId]])->whereDate('date','>=',$fromDate)->whereDate('date','<=', $toDate)->orderBy('date','Asc')->get();
        
    //     return $transactions;
    // }

    public static function getTransactionOfAccount ( $accountId, $brandIds, $fromDate=null, $toDate=null)
    {
        $transactions = [];
        // Log::info('getTransactionOfAccount', ['accountId' => $accountId, "brandId"=>$brandId,"fromDate"=>$fromDate,"toDate"=>$toDate]);
        if($fromDate === null && $toDate === null)
            $transactions = Transaction::where('accountId',$accountId)->whereIn('brandId', $brandIds)->orderBy('date','Asc')->get();
        else if($fromDate != null && $toDate === null)
            $transactions = Transaction::where('accountId',$accountId)->whereIn('brandId', $brandIds)->whereDate('date','>=',$fromDate)->orderBy('date','Asc')->get();
        else if ($fromDate === null && $toDate != null)
            $transactions = Transaction::where('accountId',$accountId)->whereIn('brandId', $brandIds)->whereDate('date','<=',$toDate)->orderBy('date','Asc')->get();
        else
            $transactions = Transaction::where('accountId',$accountId)->whereIn('brandId', $brandIds)->whereDate('date','>=',$fromDate)->whereDate('date','<=', $toDate)->orderBy('date','Asc')->get();
        
        return $transactions;
    }

    public static function getBrandCurrentBalanceOfAccountTypeAtDate($brandId, $accountType, $date)
    {
        $account = Account::where([['type','=',$accountType], ['brandID','=',$brandId]])->first();
        if(!$account)
            return 0;
        $transaction = Transaction::where('accountId',$account->id)->whereDate('date','<=',$date)->orderBy('date','Desc')->first();
        if(!$transaction)
            return 0;
        else
            $transaction = Transaction::where( [['accountId','=',$account->id]])->whereDate('date','=',$transaction->date)->orderBy('id','Desc')->first();
        
            return $transaction->currentBalance;
    }
    public static function getBrandCurrentBanksBalanceAtDate($brandId,  $date)
    {
        $accounts = Account::where([['type','=','bank']])->get();
        // Log::info("getBrandCurrentBanksBalanceAtDate",["brandId"=>$brandId,"date"=>$date,"banks"=>$accounts]);
        if(empty($accounts))
            return 0;
        $banksBalance = 0;
        foreach($accounts as $account)
        {
            $transaction = Transaction::where([['accountId',$account->id],['brandId',$brandId]])->whereDate('date','<=',$date)->orderBy('date','Desc')->first();
            // Log::info("getBrandCurrentBanksBalanceAtDate",["brandId"=>$brandId,"date"=>$date,"banks"=>$accounts,"transactions"=>$transaction]);
            if(!$transaction)
            {
                $banksBalance = $banksBalance + $account->initialBalance;
                continue;
            }
            else
                $transaction = Transaction::where( [['accountId','=',$account->id],['brandId',$brandId]])->whereDate('date','=',$transaction->date)->orderBy('id','Desc')->first();

            $banksBalance = $banksBalance + $transaction->currentBalance;
        }
        return $banksBalance;
    }
    public static function transactionsToTransactionsRows($transactions)
    {
        $transactionsRows = [];
        $totalCash = 0;
        $totalCashDollar = 0;
        $totalCustodyCash = 0;
        $totalCheck = 0;
        $totalVisa = 0;
        $totalBank = 0;
        foreach($transactions as $trans)
        {
            $transRow = new TransactionRow($trans->date, $trans->description, $trans->clientName);
            $account = Account::where('id',$trans->accountId)->first();
            if(!strcmp($account->type,"cash"))
            {
                if(!strcmp($trans->type,"add"))
                    $transRow->cash = $trans->value;
                else
                    $transRow->cash = - $trans->value;

                $totalCash += $transRow->cash;
            }
            else if(!strcmp($account->type,"custodyCash"))
            {
                if(!strcmp($trans->type,"add"))
                    $transRow->custodyCash = $trans->value;
                else
                    $transRow->custodyCash = - $trans->value;

                $totalCustodyCash += $transRow->custodyCash;
            }
            else if(!strcmp($account->type,"cashDollar"))
            {
                if(!strcmp($trans->type,"add"))
                    $transRow->cashDollar = $trans->value;
                else
                    $transRow->cashDollar = - $trans->value;

                $totalCashDollar += $transRow->cashDollar;
            }
            else if(!strcmp($account->type,"check"))
            {
                if(!strcmp($trans->type,"add"))
                    $transRow->check = $trans->value;
                else
                    $transRow->check = - $trans->value;

                $totalCheck += $transRow->check;
            }
            else if(!strcmp($account->type,"visa"))
            {
                if(!strcmp($trans->type,"add"))
                    $transRow->visa = $trans->value;
                else
                    $transRow->visa = - $trans->value;

                $totalVisa += $transRow->visa;
            }
            else if(!strcmp($account->type,"bank"))
            {
                if(!strcmp($trans->type,"add"))
                    $transRow->banks = $trans->value;
                else
                    $transRow->banks = - $trans->value;
                
                $totalBank += $transRow->banks;
            }
            array_push($transactionsRows,$transRow);
        }
        $transRow = new TransactionRow('', '', '');
        $transRow->cash = $totalCash;
        $transRow->cashDollar = $totalCashDollar;
        $transRow->custodyCash = $totalCustodyCash;
        $transRow->visa = $totalVisa;
        $transRow->check = $totalCheck;
        $transRow->banks = $totalBank;
        return array($transactionsRows, $transRow);
        // $transactionsRows = json_encode($transactionsRows);
        // return $transactionsRows;
    }

    // public static function getLatest100Transactions($brandId)
    // {
    //     $transactions = [];
    //     $transactions = Transaction::where('brandId',$brandId)->orderBy('id', 'desc')->limit(100)->get();

    // }
    public static function updateCurrentBalanceOnDeletion($transactionDate, $accountId)
    {
        $account = Account::where('id', $accountId)->first();
        $prevTransaction = Transaction::where('accountId', $accountId)->whereDate('date','<',$transactionDate)->orderBy('date','Desc')->first();
        if(!empty($prevTransaction))
            $prevTransaction = Transaction::where('accountId', $accountId)->whereDate('date','=',$prevTransaction->date)->orderBy('id','Desc')->first();

        $followingTransactions = Transaction::where('accountId', $accountId)->whereDate('date','>=',$transactionDate)->orderBy('date','Asc')->get();
        
        if(!empty($prevTransaction))
            $currentBalance = $prevTransaction->currentBalance;
        else
        {
            $currentBalance =  $account->initialBalance;
        }
            
        foreach($followingTransactions as  $trans)
        {
            if(!strcmp($trans->type,"add"))
                $currentBalance = $currentBalance + $trans->value;
            else
                $currentBalance = $currentBalance - $trans->value;
            
            // Transaction::where('id', $trans->id)-> update(['currentCashNameTotal'=>$currentBalance]);
            $trans->currentBalance = $currentBalance;
            $trans->save();
        }
    }
    public function deleteTransaction()
    {
        $transactionDate = $this->date;
        $accountId = $this->accountId;

        $deletionStatus = $this->delete();
        if($deletionStatus)
            Transaction::updateCurrentBalanceOnDeletion($transactionDate, $accountId);
        return $deletionStatus;
    }

    // public static function settleCheck($transaction)
    // {
    //     if($transaction->settled)
    //         return;
        
    //     $transaction->settled = true;

    //     // $bankTransaction = new Transaction();
        
        
    //     // $toBankAccount = Account::where('id', $transaction->toBankAccountId)->first();

    //     // $toBankAccount->balance = $toBankAccount->balance + $transaction->value;
        
    //     // DB::transaction(function () use($transaction, $toBankAccount, $bankTransaction) {
    //     //     $transaction->save();
    //     //     $toBankAccount->save();
    //     //     $bankTransaction->init($transaction->toBankAccountId, Auth::user()->id, "add", $transaction->value, $transaction->date, null, null, null, null, null ,$transaction->description, $transaction->clientName, $transaction->brandId);
    //     //     $bankTransaction->save();
    //     // }, 5);
    // }

    public static function getCurrentMonthTransactions($accountId)
    {
        $account = Account::where('id', $accountId)->first();
        if($account ===null)
            return [];
        if(Auth::user()->admin)
        {
            return Transaction::where('accountId', $account->id)->whereYear('date', Carbon::now('Egypt')->year)->whereMonth('date', Carbon::now('Egypt')->month)->get();
        }
        else
        {
            return Transaction::where([['accountId', $account->id],['brandId', Auth::user()->brandId]])->whereYear('date', Carbon::now('Egypt')->year)->whereMonth('date', Carbon::now('Egypt')->month)->get();
        }
    }

}
