<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quantity',
        'price'
    ];


    /**
     *
     * @return integer
     */
    public function getQuantity() {

        return $this->quantity;

    }


    /**
     *
     * @return string
     */
    public function getName() {

        return $this->name;

    }


    /**
     *
     * @return integer
     */
    public function getPrice() {

        return $this->price;

    }


    /**
     * Update product quantity
     *
     * @param integer $change
     * @return boolean
     */
    public function updateQuantity(int $change) {

        $newQuantity = $this->quantity + $change;

        if ($newQuantity < 0) {
            throw new \InvalidArgumentException('Product quantity can not be negative number.');
        }

        $this->quantity = $newQuantity;

        return $this->save();
    }
}
