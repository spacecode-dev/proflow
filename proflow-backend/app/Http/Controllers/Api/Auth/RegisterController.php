<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use App\UserDetail;
use App\Company;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\VerifiesEmails;
use App\Traits\ImageUpload;
use App\Traits\HeaderRequest;
use DB;
use App\Http\Transformer\UserTransformer;
use Socialite;
use Auth;
use Redirect;
use App\Role;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers, VerifiesEmails, ImageUpload, HeaderRequest;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function register(Request $request)
    {
        DB::beginTransaction();
        try {

            $data = $request->all();
            $emailInvitedExits =  User::where('email', $data['email'])
            ->where('is_issue_invited', 1)
            ->first();



            if (!$emailInvitedExits) {
                $validator = $this->validator($data);
                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()->first()], 422);
                }
                $user = User::create([
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                ]);
                $userDetail = UserDetail::create(['user_id' => $user->id]);
                $findEmailDomainName =substr($data['email'], strpos($data['email'], '@') + 1);
                $getEmailDomainName = explode('.', $findEmailDomainName);
                $getCompanyMatching =  Company::where('workspace_url', $getEmailDomainName[0])
                ->first();
                if($getCompanyMatching) {
                    $user->userDetail->update(['signup_step' => 1,'invited_by'=>  $getCompanyMatching->created_by]);

                    $user->company()->attach($getCompanyMatching->id, ['role_id' => Role::$User]);
                }
            } else {
                $emailInvitedExits->update(['password' => Hash::make($data['password'])]);
                $user =  $emailInvitedExits;
            }
            $user->sendApiEmailVerificationNotification();
            $this->updateHeaderInfo($request->header('timezone'), $request->header('subdomain'), $user);
        } catch (Exception $e) {
          //  throw $e;
            DB::rollBack();
            return response()->json(['message' => ['Something went wrong. Please try again later.']], $e->getCode());
        }
        DB::commit();
        return response()->json(['message' => ['Please confirm yourself by clicking on verify user button sent to you on your email']], 200);
    }

    public function googleAuth(Request $request)
    {
        DB::beginTransaction();
        // We'll simply execute the given callback within a try / catch block
        // and if we catch any exception we can rollback the transaction
        // so that none of the changes are persisted to the database.
        try {
            $data = $request->all();

            $emailInvitedExits =  User::where('email', $data['email'])->where('email_verified_at', null)
            ->where('is_issue_invited', 1)
            ->first();
            if ($emailInvitedExits) {
                $emailInvitedExits->update(['name' => $data['name']]);
                $emailInvitedExits->markEmailAsVerified();
                $emailInvitedExits->userDetail()::update(['user_id' => $emailInvitedExits->id, 'google_token' => $data['google_token']]);
                $user = $emailInvitedExits;
            } else {
                $user = UserDetail::getToken($data['google_token']);
                if (empty($user)) {
                    $user = User::create([
                        'email' => $data['email'],
                        'name' => $data['name']
                    ]);
                    $user->markEmailAsVerified();
                    UserDetail::create(['user_id' => $user->id, 'google_token' => $data['google_token']]);
                }
            }
            if ($request->file('profile_picture')) {
                $this->addImage($request, $user->userDetail, 'profile_picture');
            }
            
            $token = $user->createToken('proflow')->plainTextToken;
            $this->updateHeaderInfo($request->header('timezone'), $request->header('subdomain'), $user);
        }

        // If we catch an exception, we will roll back so nothing gets messed
        // up in the database. Then we'll re-throw the exception so it can
        // be handled how the developer sees fit for their applications.
        catch (\Exception $e) {
          //  throw $e;
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()]);
        }
        DB::commit();
        $userData = (new UserTransformer)->transformLogin($user, $token);
        return response()->json($userData, 200);
    }

    public function redirectToGoogle(Request $request)
    {

        return [
            'url' => Socialite::driver('google')->with(["prompt" => "select_account", 'state' => $request->header('subdomain'),'timezone' => $request->header('timezone')])->stateless()
                ->scopes(['profile'])

                ->redirect()->getTargetUrl(),
        ];
    }

    /**
     * Obtain the user information from the provider.
     *
     * @param  string $driver
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback(Request $request)
    {
        $user = Socialite::driver('google')->stateless()->user();
        $userDetail = $this->findOrCreateUser('google', $user);
        $authUser = Auth::loginUsingId($userDetail->user_id);
        $this->updateHeaderInfo($request->header('timezone'), $request->input('state'), $authUser);
        $token = $authUser->createToken('proflow')->plainTextToken;
        $subDomain = $authUser->company()->pluck('workspace_url')->first() ?? $request->input('state');
        $mainDomain = config('app.frontend_main_domain');
        return Redirect::intended('https://' . $subDomain . '.' . $mainDomain . '/oauth/' . $token);
    }

    /**
     * @param  string $provider
     * @param  \Laravel\Socialite\Contracts\User $sUser
     * @return \App\User|false
     */
    protected function findOrCreateUser($provider, $user)
    {
        $oauthProvider = UserDetail::where('google_token', $user->id)
            ->first();

        if ($oauthProvider) {
            $oauthProvider->update([
                'google_token' => $user->getId(),
            ]);

            return $oauthProvider;
        }
        $emailExits = User::where('email', $user->getEmail())->first();  
        if ($emailExits) {
            $emailExits->update(['email_verified_at'=>now()]);
            return $emailExits->userDetail;
        }

        return $this->createUser($provider, $user);
    }

    /**
     * @param  string $provider
     * @param  \Laravel\Socialite\Contracts\User $sUser
     * @return \App\User
     */
    protected function createUser($provider, $sUser)
    {
        $user = User::create([
            'name' => $sUser->getName(),
            'email' => $sUser->getEmail(),
            'email_verified_at' => now(),
        ]);


        $userDetail = UserDetail::create(['user_id' => $user->id, 'google_token' => $sUser->getId(), 'profile_picture' => $sUser->getAvatar()]);


        return  $userDetail;
    }
}
