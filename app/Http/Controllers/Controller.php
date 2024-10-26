<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use App\Models\Order;
use App\Models\OrderProcessor;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{

    public function index(Request $request) {

        $store = Product::all();

        $users = User::orderBy('id', 'asc')->with(['basket', 'wallet', 'orders'])->limit(3)->get();

        $this->sanitizeUserBaskets($users);

        if (!session('checkoutRequest', false)) {
            $this->resetSession();
        }

        return view('index', [
            'store' => $store,
            'users' => $users,
            'stats' => session('stats', []),
            'messages' => session('messages', [])
        ]);
    }



    public function checkout(Request $request) {

        // Lets pretend input data is already validated :)

        $validatedProductData = $request->input('product');

        $validatedUsersData = $request->input('users');

        $users = $this->reset($validatedProductData, $validatedUsersData);

        foreach ($users as $user) {

            $orderProcessor = new OrderProcessor($user);

            try {

                $this->setUserStat($user, 'checkout starts');

                $orderProcessor->checkout();

                $this->setUserMessage($user, 'success', 'Order settled sucessfully.');

                $this->setUserStat($user, 'checkout completed');

            } catch (\Exception $ex) {

                $this->setUserStat($user, 'checkout error');

                $this->setUserMessage($user, 'danger', $ex->getMessage());
            }
        }

        session()->flash('checkoutRequest', true);

        return redirect()->route('index');
    }


    private function sanitizeUserBaskets($users) {
        
        $productPhoneId = Product::firstWhere('name', 'Phone')->id;

        $users->each(function (User $user) use ($productPhoneId) {

            if ($user->basket->isEmpty()) {
                $basket = new Basket([
                    'user_id' => $user->id,
                    'product_id' => $productPhoneId,
                    'quantity' => 0
                ]);

                $basket->save();
                $user->refresh();
            }
        });

    }


    private function resetSession() {

        session()->forget('stats');

        session()->forget('messages');

        session()->forget('checkoutRequest');
    }


    private function reset(array $productData, array $usersData) {

        $this->resetSession();

        $users = [];

        // Process user data
        foreach($usersData as $userId => $singleUserData) {

            $user = User::find($userId);

            $user->wallet->balance = (int)$singleUserData['balance'] * 100;

            $user->wallet->save();

            $user->basket()->delete();

            foreach($singleUserData['basket'] as $basketData) {
                $basket = new Basket([
                    'user_id' => $userId,
                    'product_id' => $basketData['product_id'],
                    'quantity' => $basketData['quantity']
                ]);

                $basket->save();
            }

            $user->refresh();

            $users[] = $user;

            $this->setUserStat($user, 'checkout request');

        }

        
        // Process product
        $product = Product::find($productData['id']);

        $product->fill([
            'price' => (int)$productData['price'] * 100,
            'quantity' => (int)$productData['quantity']
        ]);

        $product->save();


        return $users;
    }


    private function setUserMessage(User $user, $messageClass, $message)
    {
        session()->flash("messages.{$user->id}", [
            'messageClass' => $messageClass,
            'message' => $message
        ]);
    }


    private function setUserStat(User $user, string $statKey, $statValue = null)
    {
        if (is_null($statValue)) {
            $statValue = now()->format('H:m:s.u');
        }

        session()->flash("stats.{$user->id}.{$statKey}", (string)$statValue);
    }
}
