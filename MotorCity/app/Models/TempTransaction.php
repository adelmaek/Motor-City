<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempTransaction extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function init($date, $accountId, $value, $comment)
    {
        $this->date = $date;
        $this->accountId = $accountId;
        $this->value = $value;
        $this->comment = $comment;
        $this->confirmed = 0;
    }
}
