<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ZeroQuantityBasketException;
use App\Exceptions\ZeroBalanceException;
use App\Exceptions\NotEnoughQuantityException;
use App\Exceptions\NotEnoughBalanceException;
use App\Exceptions\ProductNotAvailableException;

class OrderProcessor
{
    protected $order;

    protected $orderLines;

    protected $isValidated = false;

    const DEADLOCK_SOLVE_ATTEMPTS = 3;



    public function __construct(protected User $user)
    {
        $this->order = new Order([
            'user_id' => $user->id
        ]);

        $this->orderLines = collect();

        $basket = $this->user->basket;

        if (!$basket->isEmpty()) {
            $filteredNonZero = $basket->filter(function (Basket $BasketItem) {
                return $BasketItem->quantity > 0;
            });
            
            $filteredNonZero->each(function (Basket $basketItem) {

                $this->orderLines->push(new OrderLine([
                    'product_id' => $basketItem->product_id,
                    'quantity' => $basketItem->quantity,
                ]));
                
            });
        }
    }



    private function validateNonZeroQuantities() {

        if ($this->user->basket->isEmpty()) {
            throw new ZeroQuantityBasketException('Your basket is empty. Add some products and then try again.');
        }

        if ($this->orderLines->isEmpty()) {
            throw new ZeroQuantityBasketException('How many items do you need? Zero is not valid answer.');
        }
    }


    private function validateProductAvailability() {

        $this->orderLines->each(function (OrderLine $orderLine) {

            $productName = $orderLine->product->getName();

            $productQuantity = $orderLine->product->getQuantity();

            if ($productQuantity === 0) {
                throw new ProductNotAvailableException("{$productName} is not available at the moment... and for indefinite period of time actually. Sorry about that!" );
            }

            if ($orderLine->quantity > $productQuantity) {

                Basket::updateOrCreate(
                    ['user_id' => $this->user->id, 'product_id' => $orderLine->product->id],
                    ['quantity' => $productQuantity]
                );

                throw new NotEnoughQuantityException("It seems there are faster buyers out there. You have still chance to get last {$productQuantity} item(s)." );
            }
        });
    }


    private function validateUserBalance() {

        $userBalance = $this->user->getBalance();

        if ($userBalance === 0) {
            throw new ZeroBalanceException("Add some $$$ in your wallet to guarantee better shopping experience next time." );
        }

        $totalPrice = Order::getTotalPrice($this->orderLines);

        if ($totalPrice > $userBalance) {
            throw new NotEnoughBalanceException("You can not afford such a purchase at the moment." );
        }
    }


    public function validate() {

        $this->validateProductAvailability();

        $this->validateUserBalance();

        $this->validateNonZeroQuantities();

        $this->isValidated = true;
    }


    public function process() {

        if (!$this->isValidated) {
            $this->validate();
        }

        DB::transaction(function () {

            $this->order->save();

            $this->orderLines->each(function (OrderLine $orderLine) {

                $orderLine->order_id = $this->order->id;

                $orderLine->product->updateQuantity(-$orderLine->quantity);

                $orderLine->save();
            });

            $this->order->refresh();

            $totalPrice = Order::getTotalPrice($this->order->orderLines);

            $this->user->updateBalance(-$totalPrice);

            $this->user->refresh();

        }, self::DEADLOCK_SOLVE_ATTEMPTS);

    }
}
