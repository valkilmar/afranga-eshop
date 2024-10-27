<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'created_at',
        'updated_at'
    ];

    public function orderLines()
    {
        return $this->hasMany(OrderLine::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }



    public static function getTotalPrice($orderLines) {

        if (!$orderLines || $orderLines->isEmpty()) {
            return 0;
        }

        $total = 0;
        
        $orderLines->each(function (OrderLine $orderLine) use (&$total) {

            $total += $orderLine->product->price * $orderLine->quantity;

        });

        return $total;

    }

}
