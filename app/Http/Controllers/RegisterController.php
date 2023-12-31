<?php
   
namespace App\Http\Controllers;
   
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Http\Controllers\CustomerController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Mail\LaravelTenTestMail;
use Illuminate\Support\Facades\Mail;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $input = $request->all();
        $user = User::where('email', $request->email)->get();
        if(count($user) > 0 )
            return response()->json(['success' => 'false', 'msg' => 'User with email ' . $request->email . ' exist'], 200);
       
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        Auth::login($user);
        $user = CustomerController::create();
        $this->registerEmail($email);
        return $user;
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): JsonResponse
    {
        $input = $request->only('email', 'password');
        
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')->plainTextToken; 
            $success['name'] =  $user->name;
   
            try {
                // this authenticates the user details with the database and generates a token
                if (! $token = JWTAuth::attempt($input)) {
                    return $this->sendError([], "invalid login credentials", 400);
                }
            } catch (JWTException $e) {
                return $this->sendError([], $e->getMessage(), 500);
            }

            $success['token'] = $token;


            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }
    public function otp(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        $email = $request->get('email');
        $data = [
            'data' => $this->getOtp(),
            'subject' => 'Autenticazione con OTP',
            'view' => 'otp'
        ];
        Mail::to($email)->send(
            new LaravelTenTestMail($data)
        );
        return $this->sendResponse(200, 'OTP send successfully.');
    }

    public function registerEmail($email): JsonResponse {
        $data = [
            'data' => 'https://kendydrink.com/shop?subscribe=1',
            'subject' => 'Registrazione',
            'view' => 'register'
        ];
        Mail::to($email)->send(
            new LaravelTenTestMail($data)
        );
        return $this->sendResponse(200, 'OTP send successfully.');
    }

    private function getOtp() {
        return $pass= rand(100000, 999999);
    }

    public static function getCurrentUser() {
        $user = Auth::user();
        return $user;
    }

    public static function test() {
        $stripe = new \Stripe\StripeClient('sk_test_4eC39HqLyjWDarjtT1zdp7dc');
        
        $stripe->customers->createSource(
          'cus_9s6XKzkNRiz8i3',
          ['source' => 'tok_mastercard']
        );
    }
}