<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function orders()
    {
        return $this->hasMany(Order::class);
    }


    public function basket()
    {
        return $this->hasMany(Basket::class);
    }


    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }


    public function getBalance() {

        return $this->wallet->getBalance();

    }


    public function setBalance(int $balance) {

        return $this->wallet->setBalance($balance);

    }


    public function updateBalance(int $change) {

        return $this->wallet->updateBalance($change);

    }


    public function emptyBasket() {

        if ($this->basket->isEmpty()) {
            return false;
        }

        $this->basket()->delete();

        return true;
    }
}
