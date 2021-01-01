<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Bank;
use App\Models\Brand;
use Log;
use Symfony\Component\ErrorHandler\Debug;

class AccountController extends Controller
{
    public function getAddAccount()
    {
        $banks = Bank::all();
        $brands = Brand::all();
        return view('accounts/addAccount',["banks"=>$banks, "brands"=>$brands]);
    }
    //
    public function postInsertAccount(Request $request)
    {
        // $bank = Bank::where('name',$request['bankNameInput']);
        $bank = Bank::firstOrNew([
                    'name' => $request['bankNameInput']
                    ]);
        $bank->save();
        // Log::debug($bank);
        $account = new Account();
        $account->init($request['nameInput'], $request['accountType'], $request['valueInput'], $bank->id, $request['brandInput']);
        $account->push();
        // Log::debug('account saved');
        return redirect()->back();
    }

    
}