<?php

namespace App\Models;


class TransactionRow
{
    public $date;
    public $cash;
    public $custodyCash;
    public $cashDollar;
    public $check;
    public $visa;
    public $banks;
    public function __construct($date) 
    {
      $this->date = $date;
      $this->cash = 0;
      $this->custodyCash = 0;
      $this->cashDollar = 0;
      $this->check = 0;
      $this->visa = 0;
      $this->banks = 0;
    }

}