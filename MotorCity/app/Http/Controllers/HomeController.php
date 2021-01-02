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
        $transactions = [];
        $transactions = Transaction::where('userId', Auth::user()->id)->limit(20)->orderBy('id','Desc')->get();
        return view('home',["banks"=>$banks, "bankAccounts"=>$bankAccounts, "brands"=>$brands, "currentUserAccounts"=>$currentUserAccounts, 'transactions'=>$transactions] );
    }
}
