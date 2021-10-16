<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;



    public function sendResetLinkEmail(Request $request)
    {
    try {
    //Retrieve the user from the database
    $email = $request->email;
    $response = Password::sendResetLink(['email' => $email]);

    switch ($response) {
        case Password::RESET_LINK_SENT:
            return response()->json(['message' => trans('passwords.sent')], 200);

        default:
        return response()->json(['status' => 'error', 'message' => trans($response)], 500);

    }
    } catch (\Exception $e) {
        return response()->json(['message' => trans('auth.failed_response')], $e->getCode());
    }
    }

}
