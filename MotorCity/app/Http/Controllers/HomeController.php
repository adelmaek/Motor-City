<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Bank;
use App\Models\Brand;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Log;
use Carbon\Carbon;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $banks = Bank::all();
        $bankAccounts = Account::getBankAccounts();
        $brands = Brand::all();
        $currentUserAccounts = Auth::user()->getCurrentUserAccounts();
        foreach($currentUserAccounts as $acc)
        {
            if(!strcmp($acc->type,"bank"))
                $accountName = Bank::where('id', $acc->bankID)->first()->name . " " . $acc->name;
            else
            {
                $accountName = str_replace(":"," ", $acc->name);
                $accountName = str_replace("posCommission", "POS commission", $accountName);
            }
            $acc->setAttribute('accountName', $accountName);
        }
        $transactions = [];
        $transactions = Transaction::where('userId', Auth::user()->id)->limit(100)->orderBy('id','Desc')->get();
        foreach($transactions as $transaction)
        {
            $account = Account::where('id', $transaction->accountId)->first();
            $accountName = '';
            if(!strcmp($account->type,'bank'))
                $accountName = Bank::where('id', $account->bankID)->first()->name . " " .  $account->name;
            else 
                $accountName = str_replace(":"," ", $account->name);

            $transaction->setAttribute('accountName',$accountName);
        }
        $today = Carbon::today()->toDateString();
        $posAccounts = Account::getPosAccounts();
        return view('home',['posAccounts'=>$posAccounts,"banks"=>$banks, "bankAccounts"=>$bankAccounts, "brands"=>$brands, "currentUserAccounts"=>$currentUserAccounts, 'transactions'=>$transactions,'today'=>$today] );
    }
}
