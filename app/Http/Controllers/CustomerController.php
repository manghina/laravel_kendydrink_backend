<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function create(Request $request) {
        $body = json_decode($request->getContent(), true);
        $name = $body['name'];
        $email = $body['email'];

        $id = Customer::select('id')->where('email', $email)->limit(1)->get();
        if(count($id) > 0) {
            return Response::json([
                'status' => false,
                'error' => "a customer is already registred with email: $email"
            ], 406);

        }
        $client = new Customer();
        $client->name = $name;
        $client->email = $email;
        $client->save();
        
        return Response::json([
            'status' => true
        ], 201);

    }
}
