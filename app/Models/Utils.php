<?php

namespace App\Models;

use App\Models\Basket;
use App\Models\Product;
use App\Models\User;


class Utils
{

    public static function getTargetProduct() {

        return Product::firstWhere('name', 'Phone');

    }


    public static function getConqurentBuyers() {

        $users = User::orderBy('id', 'asc')->with(['basket', 'wallet', 'orders'])->limit(3);

        self::sanitizeBaskets($users);

        return $users->get();

    }


    public static function sanitizeBaskets($users) {

        $targetProduct = self::getTargetProduct();
        
        $users->each(function (User $user) use ($targetProduct) {

            if ($user->basket->isEmpty() || !$user->basket->firstWhere('product_id', $targetProduct->id)) {

                $basket = new Basket([
                    'user_id' => $user->id,
                    'product_id' => $targetProduct->id,
                    'quantity' => 0
                ]);

                $basket->save();

                $user->refresh();
            }
        });

    }


    public static function setupStage(array $productData = [], array $userData = []) {

        self::resetSession();

        // Process product
        $product = self::getTargetProduct();

        $product->fill([
            'price' => (int)$productData['price'] * 100,
            'quantity' => (int)$productData['quantity']
        ]);

        $product->save();



        // Process user data
        foreach($userData as $userId => $singleUserData) {

            $user = User::find($userId);

            $user->setBalance((int)$singleUserData['balance'] * 100);

            $user->basket()->delete();

            $basket = new Basket([
                'user_id' => $userId,
                'product_id' => $product->id,
                'quantity' => $singleUserData['quantity']
            ]);

            $basket->save();

            $user->refresh();
        }
    }


    public static function resetStage() {

        $targetProduct = self::getTargetProduct();

        $productData = [
            'id' => $targetProduct->id,
            'price' => 20,
            'quantity' => 8
        ];

        $users = self::getConqurentBuyers();

        $userData = [];

        $users->each(function($user) use(&$userData, $targetProduct) {

            $user->orders()->delete();

            $user->basket()->delete();

            $userData[$user->id] = [
                'balance' => 100,
                'quantity' => 5
            ];
        });

        self::setupStage($productData, $userData);
    }


    public static function resetSession()
    {

        session()->forget('stats');

        session()->forget('messages');

        session()->forget('checkoutRequest');
    }



    public static function setUserMessage(User $user, $messageClass, $message)
    {
        session()->flash("messages.{$user->id}", [
            'messageClass' => $messageClass,
            'message' => $message
        ]);
    }


    public static function setUserStat(User $user, string $statKey, $statValue = null)
    {
        if (is_null($statValue)) {
            $statValue = now()->format('H:m:s.u');
        }

        session()->flash("stats.{$user->id}.{$statKey}", (string)$statValue);
    }


    public static function getUserMessages() {

        return session('messages', []);

    }


    public static function getUserStats() {
        
        return session('stats', []);

    }
}
