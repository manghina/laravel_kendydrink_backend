<?php

namespace App\Http\Controllers;

  

use Illuminate\Http\Request;

use Mail;

use App\Mail\DemoMail;

  

class MailController extends Controller

{

    /**

     * Write code on Method

     *

     * @return response()

     */

     public function index()
     {
         $testMailData = [
             'title' => 'Test Email From AllPHPTricks.com',
             'body' => 'This is the body of test email.'
         ];
 
         Mail::to('manghina.dario@gmail.com')->send(new DemoMail($testMailData));
 
         return 'Success! Email has been sent successfully';
     }

}