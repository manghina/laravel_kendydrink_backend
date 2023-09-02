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
        // $body = json_decode($request->getContent());
        $body = (Object)$request->all();
        
        $validator = Validator::make($request->all(), [
            'num' => 'required',
            'elapse' => 'required',
            'cvc' => 'required'
        ]);
        
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $cardNumber = $body->num;
        $elapseDate = $body->elapse;
        $cvc = $body->cvc;



        //$user = ['none'];
        //$email = $user->email;
        $stripe = new \Stripe\StripeClient("sk_test_4eC39HqLyjWDarjtT1zdp7dc");
        $data = [
            "type" => "ach_credit_transfer",
            "currency" => "usd",
            "owner" => [
                "email" =>  'email'
            ]
            ];
        return $data;
        //$stripe->sources->create($data);
    }
}
