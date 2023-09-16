<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Auth;

class CardController extends BaseController
{
    
    public function __construct() {
        $this->middleware('auth');
    }

    public function create(Request $request) {
        $body = json_decode($request->getContent());
        
        $validator = Validator::make($request->all(), [
            'num' => 'required',
            'elapse' => 'required',
            'cvc' => 'required'
        ]);
        
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $cardNumber = $request->num;
        $elapseDate = $request->elapse;
        $cvc = $request->cvc;

        $user = Auth::user();
        $email = $user->email;
        $token = bcrypt(uniqid());
        $stripe = new \Stripe\StripeClient("sk_test_4eC39HqLyjWDarjtT1zdp7dc");
        $data = [
            "type" => "ach_credit_transfer",
            "currency" => "usd",
            "owner" => [
                "email" =>  $email
            ],
            "token" => $token
        ];
        $source = $stripe->sources->create($data);
        $card_id = $this->createCard();
        $user->card_id = $card_id;
        $user->save();
        return response()->json(['success' => 'success'], 200);
    }

    public static function createCard() {
        $stripe = new \Stripe\StripeClient('sk_test_4eC39HqLyjWDarjtT1zdp7dc');
        $user = RegisterController::getCurrentUser();
        $card = $stripe->customers->createSource(
            $user->stripe_id,
          ['source' => 'tok_mastercard']
        );
        return $card->id;
    }
    public function test() {

    }

}
