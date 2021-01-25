<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Bank;
use App\Models\Brand;
use Log;
use session;
use Symfony\Component\ErrorHandler\Debug;

class AccountController extends Controller
{
    public function getAddAccount()
    {
        $banks = Bank::all();
        $brands = Brand::all();
        $bankAccounts = Account::getBankAccounts();
        return view('accounts.addAccount',["bankAccounts"=>$bankAccounts ,"banks"=>$banks, "brands"=>$brands]);
    }
    //
    public function postInsertAccount(Request $request)
    {
        // $bank = Bank::where('name',$request['bankNameInput']);
        if(!strcmp($request['accountType'],"bank"))
        {
            if($request['bankNameInput'] === null)
                return redirect()->back();

            $bank = Bank::firstOrNew([
                'name' => $request['bankNameInput']
                ]);
            $bank->save();
            $account = new Account();
            $account->init($request['nameInput'], $request['accountType'], $request['valueInput'], $bank->id, $request['brandInput'], null);
            $account->push();
        }
        elseif(!strcmp($request['accountType'],"visa"))
        {
            if($request['posBankAccountInput'] === null)
                return redirect()->back();
                
            $account = new Account();
            $account->init($request['nameInput'], $request['accountType'], $request['valueInput'], null, $request['brandInput'], $request['posBankAccountInput']);
            $account->push();
        }
        
        // Log::debug($bank);
        // Log::debug('account saved');
        return redirect()->back();
    }
    public function postAddBrand(Request $request)
    {
        $brand = new Brand();
        $brand->name = $request['nameInput'];
        $brand->save();
        return redirect()->back();
    }
    
}