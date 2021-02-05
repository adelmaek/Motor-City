<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'name',
        'type',
        'balance',
        'initialBalance'
    ];
    public function init($name = null, $type = null, $initialBalance = null, $bankID = null, $brandID = null, $bankAccountId=null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->balance = $initialBalance;
        $this->initialBalance = $initialBalance;
        if($bankID != null) // This to be used if i added a general account addition. I mean that the account shouldn't be belonging to bank. (Possible enhancement)
            $this->bankID = $bankID;
        $this->brandID = $brandID;
        if($bankAccountId != null)
            $this->bankAccountId = $bankAccountId;
    }

    public static function getBankAccounts()
    {
        $user = auth()->user();
        if(!$user)
            return Account::where('type',"bank")->get();
        if($user->admin == 0) //make bank accounts brandless
            $bankAccounts = Account::where('type',"bank")->get();
            // $bankAccounts = Account::where('brandID',$user->brandId)->where('type',"bank")->get();
        else
            $bankAccounts = Account::where('type',"bank")->get();
        
        return $bankAccounts;
    }

    public static function getPosAccounts()
    {
        $user = auth()->user();
        if(!$user)
            return Account::where('type',"visa")->get();
        if($user->admin == 0) 
            $posAccounts = Account::where('type',"visa")->get();
            // $posAccounts = Account::where('brandID',$user->brandId)->where('type',"visa")->get();
        else
            $posAccounts = Account::where('type',"visa")->get();
        
        return $posAccounts;
    }
}
