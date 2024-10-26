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


    public function getProducts() {

        $response = collect();

        $this->orderLines()->each(function (OrderLine $orderLine) use ($response) {

            if ($orderLine->product) {
                $response->push($orderLine->product);
            }
        });

        return $response;
    }


    public function getTotalPrice() {

        $products = $this->getProducts();

        if ($products->isEmpty()) {
            return 0;
        }

        return $products->sum('price');

    }

}
