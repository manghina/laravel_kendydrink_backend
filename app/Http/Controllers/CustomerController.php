<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{

    public static function create()
    {

        $user = Auth::user();
        $name = $user->name;
        $email = $user->email;

        $stripe = new \Stripe\StripeClient('sk_test_4eC39HqLyjWDarjtT1zdp7dc');

        $stripeCustomer = $stripe->customers->create([
            'name' => $name,
            'email' => $email
        ]);
        $stripeId = $stripeCustomer->id;
        // register api
        $data = [
            'password' => 'password',
            'email' => $email,
            'name' => $name,
            'stripe_id' => $stripeId
        ];

        $user->stripe_id = $stripeId;
        $user->save();
        return $user;
    }

    public function get($id)
    {
        return User::where('id', $id)->get();
    }

    private function getStripeId($id)
    {
        $user = User::where('id', $id)->get();
        if (empty($user))
            return "No user found with id:" . $body['id'];
        $user = $user[0];
        return $user->stripe_id;
    }

    public function update(Request $request)
    {
        $body = json_decode($request->getContent(), true);
        $data = [];
        if (!isset($body['id']))
            return "No user id provided";
        $user = User::find($body['id']);
        if (empty($user))
            return "No user found with id:" . $body['id'];
        if (isset($body['name']))
            $user->name = $body['name'];

        if (isset($request['email']))
            $user->email = $body['email'];

        if (isset($body['card_id']))
            $user->card_id = $body['card_id'];
        $user->save();
        return $user;
    }

    public function delete($id)
    {
        /*
        $user = $this->get($id);
        $stripeId = $this->getStripeId($id);
        User::delete($id);
        $stripe = new \Stripe\StripeClient('sk_test_4eC39HqLyjWDarjtT1zdp7dc');
        $stripe->customers->delete(
          $stripeId,
          []
        );
        */
    }

    public function getclientlist($id)
    {
        // id: 0->all users, 1: customers, 2: influencer
        $userlist = [];
        if ($id == 0) {
            $userlist = User::get();
        }else if($id == 1) {
            $userlist = User::where('role', 'CUSTOMER')->get();
        }else {
            $userlist = User::where('role', 'INFLUENCER')->get();
        }

        return $userlist;
    }

    public function createInfluenerUser(Request $request)
    {
        $influencer_customer = new User();
        $influencer_customer->name = $request['name'];
        $influencer_customer->surname = $request['surname'];
        $influencer_customer->email = $request['email'];
        $influencer_customer->phone_number = $request['phone_number'];
        $influencer_customer->password = $request['password'];
        $influencer_customer->percentage = $request['discount'];
        $influencer_customer->role = 'INFLUENCER';
        $influencer_customer->referral = $request['referral'];
        $influencer_customer->save();
        $userlist = User::where('role', 'INFLUENCER')->get();

        return $userlist;
    }
}