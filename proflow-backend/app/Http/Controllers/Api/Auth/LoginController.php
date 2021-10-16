<?php
namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Transformer\UserTransformer;
use Exception;


class LoginController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('throttle:20,1')->only('login');
        //   $this->middleware('verified');

    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required',
        ]);
    }

    public function login(Request $request)
    {
        try {
            $data = $request->all();
            $validator = $this->validator($data);
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first()], 422);
            }
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                // Authentication passed...
                $user = Auth::user();

            if(!$user->email_verified_at){

                return response()->json(['message'=>trans('auth.not_verified_email')], 401);
            }
            $token = $user->createToken('proflow')->plainTextToken;
            $message = trans('auth.success_login');
            $userData = (new UserTransformer)->transformLogin($user, $token, $message);
            return response()->json($userData, 200);

        }

        return response()->json([ 'message' => trans('auth.failed_login')], 422);
    }
        catch (Exception $e){
            return response()->json(['message' => trans('auth.failed_response')], $e->getCode());
        }


    }


}
