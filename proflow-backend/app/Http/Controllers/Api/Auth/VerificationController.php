<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use App\User;
use Redirect;
use config;
use Carbon\Carbon;


class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */



    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        // $this->middleware('signed')->only('verify');
        //  $this->middleware('throttle:6,1')->only('verify', 'resend');
        $this->redirectTo = config('app.frontend_url');
    }



    public function verify(Request $request)
    {
        $frontend_url = config('app.frontend_url');
        $url = "http://{$frontend_url}";
        try {
            $userID = $request->route('id');
            $user = User::findOrFail($userID);
            if ($user) {
                $company =  $user->company()->select('companies.name', 'companies.workspace_url', 'companies.created_by')->orderBy('companies.created_at', 'desc')->first();

                if ($company) {
                    $subDomain = $company['workspace_url'];
                } else {
                    $subDomain = config('app.frontend_staging_domain');
                }

                $mainDomain = config('app.frontend_main_domain');

                $url = "http://{$subDomain}.{$mainDomain}";

                if ($user->hasVerifiedEmail()) {
                    return Redirect::to($url . '?is_already_verified=true');
                }

                if (!hash_equals((string) $request->route('id'), (string) $user->getKey())) {
                    return Redirect::to($url . '?is_expired=true&email=' . $user->email);
                }

                if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
                    return Redirect::to($url . '?is_expired=true&email=' . $user->email);
                }
                $expire=$request->get('expires');
              
                $date = Carbon::now()->getTimestamp();
                if ($date > $expire) {
                    return Redirect::to($url . '?is_expired=true&email=' . $user->email);
                }

                $user->markEmailAsVerified();
                return Redirect::to($url . '?is_verified=true');
            }
            return Redirect::to($url . '?is_expired=true&email=' . $user->email);
        } catch (\Exception $e) {
            return Redirect::to($url . '?email=' . $user->email);
        }
    }


    public function resend(Request $request)
    {

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->sendApiEmailVerificationNotification();
            return response()->json(['message' => trans('auth.send_email')], 200);
        }


        return response()->json(['message' => trans('passwords.user')], 500);
    }
}
