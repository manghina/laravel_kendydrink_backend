<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billing;

class Billingcontroller extends Controller
{
    public function all()
    {
        return response()->json(Billing::all());
    }

    public function get($id)
    {
        return response()->json(
            Billing::where('id', $id)->get()
        );
    }

    public function create($id)
    {
        $billing = new Billing();
        $billing->name = $request['name'];
        $billing->surname = $request['surname'];
        $billing->email = $request['email'];
        $billing->phoneNumber = $product['phoneNumber'];
        $billing->city = $product['city'];
        $billing->country = $product['country'];
        $billing->shippingAddress = $product['shippingAddress'];
        $billing->zipCode = $product['zipCode'];
        $billing->save();

        return response()->json(
            $billing
        );
    }


}
