<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'balance'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function getBalance() {

        return $this->balance;
        
    }

    /**
     * Update user balance
     *
     * @param integer $change
     * @return boolean
     */
    public function updateBalance(int $change) {

        $newBalance = $this->balance + $change;

        if ($newBalance < 0) {
            throw new \InvalidArgumentException('Balance can not be negative number.');
        }

        $this->balance = $newBalance;

        return $this->save();
    }
}
