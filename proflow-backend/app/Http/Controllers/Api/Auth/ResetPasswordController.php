<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Auth;
use Hash;
use App\User;
use Redirect;
use Carbon\Carbon;
use App\PasswordReset;
use App\Notifications\PasswordResetSuccess;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Auth\PasswordBroker;

class ResetPasswordController extends Controller
{
  /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

  use ResetsPasswords;

  /**
   * Where to redirect users after resetting their password.
   *
   * @var string
   */
  protected $redirectTo = RouteServiceProvider::HOME;


  public function updatePasssword(Request $request)
  {
    $data = $request->all();
    $user = Auth::user(); // or pass an actual user here
    $validator = Validator::make($data, [
      'current_password' => 'required|password',
      'new_password' => 'sometimes|required|string|min:6|different:current_password',

    ]);

    //validation fail
    if ($validator->fails()) {
      return response()->json(['message' => $validator->errors()->first()], 422);
    }
    if (isset($request['new_password'])) {
      $user->password = Hash::make($request['new_password']);
      if ($user->save()) {
        return response()->json(['status' => 'success', 'message' => trans('passwords.update_success')], 200);
      }
      return response()->json(['status' => 'error', 'message' => trans('common.app_error')], 500);
    }
    return response()->json(['status' => 'success'], 200);
  }


  public function showResetForm(Request $request)
  {

    $frontend_url = config('app.frontend_url');
    $url = $frontend_url;
    $user = PasswordReset::where('email', $request->email)
      ->where('created_at', '>', Carbon::now()->subHours(1))
      ->first();
     
    if ($user) {
      return Redirect::to("{$url}/reset-password?email={$request->email}&token={$request->token}");
    }
    return  Redirect::to("{$url}/reset-password?is_expired=true");
  }


  public function reset(Request $request)
  {
    $data = $request->all();
    $validator = Validator::make($data, [
      'email' => 'required|string|email',
      'password' => 'required|string',
      'token' => 'required|string'
    ]);
    //validation fail
    if ($validator->fails()) {
      return response()->json(['message' => $validator->errors()->first()], 422);
    }

    $password = $request->password;
    $passwordReset =
      $response = Password::reset($request->all(), function ($user, $password) {
        $user->password = bcrypt($password);
        $user->save();
        $user->notify(new PasswordResetSuccess());
      });

    switch ($response) {
      case PasswordBroker::PASSWORD_RESET:

        return response()->json(['status' => 'success', 'message' => trans('passwords.reset')], 200);
      default:
        return response()->json(['status' => 'error', 'message' => trans($response)], 500);
    }
  }
}
