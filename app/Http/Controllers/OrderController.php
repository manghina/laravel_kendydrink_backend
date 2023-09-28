<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function checkout(Request $request) {
        
        $stripe = new \Stripe\StripeClient('sk_test_51NGgNzGfXypnSGPSkdMqKlzm59UbUjgC7i0KsfIW0YmpuYjEly1EI0mm0KMO8biFQEbXEpVnKAg4fdet1NJxuUAR00kgBEPlPP');
        $cart = $request->all();
        file_put_contents("asdsadsada", print_r($cart, true));
        if(!count($cart))
            return "Bad request: Cannot checkout an empty cart";
        $cart= $request->get('cart');
        foreach ($cart as $key => $product) {
            if(!isset($product['id']))
                return "Bad request: one or more cart items does not contain id field";

            if(!isset($product['quantity']))
                return "Bad request: one or more cart items does not contain quantity field";
        }


        $amount = 0;
        $prices = [];
        $checkout = [];
        foreach ($cart as $product) {
            $item = [];
            $id =  $product['id'];
            $row = Product::where('id', $id)->get();
            if(count($row) == 0) {
                return Response::json([
                    'status' => false,
                    'error' => "Product with id: $id not found"
                ], 406);            
            }
            $row = $row[0];
            $item['price'] = $row->price;
            $item['quantity'] = $product['quantity'];
            $paymentRequest['line_items'] []= $item;
            $amount += ($row->price * $product['quantity']);

            $products[]= [
                'product_id' => $id,
                'price' => $row->price,
                'quantity' => $product['quantity'],
                'subtotal' => $amount,
                'stripe_test_id' => $row->stripe_test_id
            ];

            $payload =
                [
                    'currency' => 'eur',
                    'product' => $row->stripe_test_id,
                    'unit_amount' => $product['quantity'],
                ];
                // $stripeProduct = $stripe->prices->create($payload);
                $checkout []= [
                    'price' => $row->stripe_test_id,
                    'quantity' => $product['quantity'],
                ];    
            }

            // return $checkout;


            
            $response = [
                'line_items' =>  $checkout,
                'mode' => 'payment',
                'success_url' => 'http://127.0.0.1:8000/success.html',
                'cancel_url' => 'http://127.0.0.1:8000/cancel.html',
            ];
            //return $response;
            \Stripe\Stripe::setApiKey("sk_test_51NGgNzGfXypnSGPSkdMqKlzm59UbUjgC7i0KsfIW0YmpuYjEly1EI0mm0KMO8biFQEbXEpVnKAg4fdet1NJxuUAR00kgBEPlPP");
            $checkout_session = \Stripe\Checkout\Session::create($response);
              
              return json_encode(['url' => $checkout_session->url]);
            
            
            
            //return  $stripe->paymentLinks->create(['line_items' => $response]);                
            $this->create(10, 'card_1NlVwN2eZvKYlo2Czx1c9iGM', $products);
            return $response;

    } 

    public function test(Request $request) {
        return Auth::user();
    }

    public function getOrderNumber() {
        $order =  DB::table('orders')
        ->select('orders.order_id',DB::raw('ifnull(sum(orders.order_id), 0)'))
        ->groupBy('orders.order_id')
        ->get();
        if(!count($order)) {
            $orderId = 1;
        } else {
            $orderId = $order[0]->order_id;
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
