<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\DB;

class OrderProcessor
{
    protected $order;

    protected $orderLines;

    const DEADLOCK_SOLVE_ATTEMPTS = 3;

    public function __construct(protected User $user)
    {
        $this->order = new Order([
            'user_id' => $user->id
        ]);

        $this->orderLines = collect();
    }


    private function validateNonZeroQuantities() {
        
        $basket = $this->user->basket;

        if ($basket->isEmpty()) {
            throw new \InvalidArgumentException('User basket is empty.');
        }

        $filteredNonZero = $basket->filter(function (Basket $BasketItem) {
            return $BasketItem->quantity > 0;
        });
         
        if ($filteredNonZero->isEmpty()) {
            throw new \InvalidArgumentException('Only zero quantities found in user basket.');
        }
        
        $filteredNonZero->each(function (Basket $basketItem) {

            $this->orderLines->push(new OrderLine([
                'product_id' => $basketItem->product_id,
                'quantity' => $basketItem->quantity,
            ]));
            
        });
    }


    private function validateProductAvailability() {

        $this->orderLines->each(function (OrderLine $orderLine) {

            $productName = $orderLine->product->getName();

            $productQuantity = $orderLine->product->getQuantity();

            if ($productQuantity === 0) {
                throw new \InvalidArgumentException("{$productName} not available." );
            }

            if ($orderLine->quantity > $productQuantity) {
                throw new \InvalidArgumentException("{$productName} not sufficient quantity." );
            }
        });
    }


    private function validateUserBalance() {

        $totalPrice = $this->order->getTotalPrice();

        $userBalance = $this->user->getBalance();

        if ($totalPrice > $userBalance) {
            throw new \InvalidArgumentException("User balance not sufficient." );
        }
    }


    private function validate() {

        $this->validateNonZeroQuantities();

        $this->validateProductAvailability();

        $this->validateUserBalance();

    }


    private function process() {

        DB::transaction(function () {

            $this->order->save();

            $this->orderLines->each(function (OrderLine $orderLine) {

                $orderLine->order_id = $this->order->id;

                $orderLine->product->updateQuantity(-$orderLine->quantity);

                $orderLine->save();
            });

            $this->order->refresh();

            $totalPrice = $this->order->getTotalPrice();

            $this->user->updateBalance(-$totalPrice);

            $this->user->emptyBasket();

            $this->user->refresh();

        }, self::DEADLOCK_SOLVE_ATTEMPTS);

    }


    /**
     * Transform user basket to an order and updates user balance & product quantities.
     *
     * @return boolean
     * @throws Exception
     */
    public function checkout() {

        $this->validate();

        $this->process();

        return true;
    }
}
