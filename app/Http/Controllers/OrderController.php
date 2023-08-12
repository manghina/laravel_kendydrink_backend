<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Response;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        $stripe = new \Stripe\StripeClient('sk_test_51NGgNzGfXypnSGPSkdMqKlzm59UbUjgC7i0KsfIW0YmpuYjEly1EI0mm0KMO8biFQEbXEpVnKAg4fdet1NJxuUAR00kgBEPlPP');

        $cart = json_decode($request->getContent());
        $paymentRequest = [
            'success_url' => 'https://dropmail.me/it/',
            'line_items' => [],
            'mode' => 'payment',
        ];
        $amount = 0;

        if(!count($cart))
            return "Bad request: Cannot checkout an empty cart";
        foreach ($cart as $key => $product) {
            if(!isset($product->id))
                return "Bad request: one or more cart items does not contain id field";

            if(!isset($product->quantity))
                return "Bad request: one or more cart items does not contain quantity field";
        }

        foreach ($cart as $key => $product) {
            $item = [];
            $id =  $product->id;
            $row = Product::where('id', $id)->get();
            if(count($row) == 0) {
                return Response::json([
                    'status' => false,
                    'error' => "Product with id: $id not found"
                ], 406);            
            }
            $row = $row[0];
            $item['price'] = $row->price;
            $item['quantity'] = $product->quantity;
            $paymentRequest['line_items'] []= $item;
            $amount += ($row->price * $product->quantity);
        }
        $amount *= 100;

        $stripe->charges->create([
            'amount' => $amount,
            'currency' => 'eur',
            'source' => 'tok_amex',
            'description' => 'Kendy Drink Order',
          ]);
          return Response::json([
              'status' => true
          ], 200);

        return json_encode($paymentRequest);
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $stripe->checkout->sessions->create($paymentRequest);

    }
}
