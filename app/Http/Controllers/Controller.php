<?php

namespace App\Http\Controllers;

use App\Exceptions\NotEnoughBalanceException;
use App\Exceptions\NotEnoughQuantityException;
use App\Exceptions\ProductNotAvailableException;
use App\Exceptions\ZeroBalanceException;
use App\Exceptions\ZeroQuantityBasketException;
use App\Http\Requests\CheckoutRequest;
use App\Models\OrderProcessor;
use App\Models\Utils;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{

    public function index(Request $request) {

        $product = Utils::getTargetProduct();

        $users = Utils::getConqurentBuyers();

        if (!session('isCheckoutRequest', false)) {

            Utils::resetSession();

        }

        return view('index', [
            'product' => $product,
            'users' => $users,
            'stats' => Utils::getUserStats(),
            'messages' => Utils::getUserMessages()
        ]);
    }


    public function reset(Request $request) {

        Utils::resetStage();

        Utils::resetSession();

        return redirect()->route('index');
    }



    public function checkout(CheckoutRequest $request) {
        
        $productData = $request->validated('product');
        
        $userData = $request->validated('users');

        Utils::setupStage($productData, $userData);

        $users = Utils::getConqurentBuyers();

        $users = $users->shuffle();

        foreach ($users as $user) {

            $orderProcessor = new OrderProcessor($user);

            try {

                Utils::setUserStat($user, 'order validation starts');

                $orderProcessor->validate();

                Utils::setUserStat($user, 'order is valid');

                Utils::setUserStat($user, 'processing order');

                $orderProcessor->process();

                Utils::setUserStat($user, 'checkout completed');

                Utils::setUserMessage($user, 'success', "Congratulations, you've got it! You feel lucky today, huh? ;)");

            } catch (ZeroBalanceException $ex) {

                Utils::setUserStat($user, 'zero balance');

                Utils::setUserMessage($user, 'danger', $ex->getMessage());

            } catch (NotEnoughBalanceException $ex) {

                Utils::setUserStat($user, 'not enough balance');

                Utils::setUserMessage($user, 'warning', $ex->getMessage());

            } catch (NotEnoughQuantityException $ex) {

                Utils::setUserStat($user, 'not enough quantity');

                Utils::setUserMessage($user, 'warning', $ex->getMessage());

            } catch (ZeroQuantityBasketException $ex) {

                Utils::setUserStat($user, 'zero basket quantity');

                Utils::setUserMessage($user, 'warning', $ex->getMessage());

            } catch (ProductNotAvailableException $ex) {

                Utils::setUserStat($user, 'zero product quantity');

                Utils::setUserMessage($user, 'danger', $ex->getMessage());

            } catch (Exception $ex) {

                Utils::setUserStat($user, 'general error');

                Utils::setUserMessage($user, 'danger', $ex->getMessage());
            }
        }

        session()->flash('isCheckoutRequest', true);

        return redirect()->route('index');
    }
}
