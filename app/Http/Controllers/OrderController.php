<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function checkout(Request $request) {
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
        $products = [];
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

            $products[]= [
                'product_id' => $id,
                'price' => $row->price,
                'quantity' => $product->quantity,
                'subtotal' => $amount
            ];
        }

        $amount *= 100;
        /*
            $stripe->charges->create([
                'amount' => $amount,
                'currency' => 'eur',
                'source' => 'tok_amex',
                'description' => 'Kendy Drink Order',
            ]);

            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $stripe->checkout->sessions->create($paymentRequest);
        */

        // SESSION => USER_ID
        $this->create(10, 'card_1NlVwN2eZvKYlo2Czx1c9iGM', $products);

    }
    public function test() {
        // return $this->create(10, 'card_1NlVwN2eZvKYlo2Czx1c9iGM', $products);
    }

    public function getOrderNumber() {
        $orderId = DB::table('orders')
        ->select('orders.order_id',DB::raw('ifnull(sum(orders.order_id), 0)'))
        ->groupBy('orders.order_id')
        ->get();
        if(!count($orderId)) {
            $orderId = 1;
        } else {
            $orderId += 1;
        }
        return $orderId;
    }

    public function create($user_id, $card_id, $products) {
        $orderId = $this->getOrderNumber();

        foreach ($products as $product) { 
            $order = new Order();
            $order->order_id = $orderId;
            $order->user_id = $user_id;
            $order->card_id = $card_id;
            $order->product_id = $product['product_id'];
            $order->quantity = $product['quantity'];
            $order->price_unit = $product['price'];
            $order->subtotal_row = $product['subtotal'];
            $order->save();
        }
    }

}
