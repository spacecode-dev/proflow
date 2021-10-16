<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use DateTimeZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Transformer\UserTransformer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendInviteEmail;
use App\Traits\ImageUpload;
use DateTime;
use Exception;
use App\Company;
use App\User;
use App\Role;

class UserDetailController extends Controller
{
    use ImageUpload;

    /**
     * @return JsonResponse
     */
    public function getProfile()
    {
        $user = Auth::user();
        $userDetail = $user->userDetail;
        $userData = (new UserTransformer)->transformUserDetail($user);
        return response()->json($userData, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function profileDetail(Request $request)
    {
        $user = Auth::user();
        $userDetail = $user->userDetail;
        $user->name = isset($request->name) ? $request->name : '';
        $userDetail->department_id = isset($request->department_id) ? $request->department_id : '';
        $userDetail->managing_people = isset($request->managing_people) ? $request->managing_people : '';
        if ($user->save() && $userDetail->save()) {
            $user->userDetail->update(['signup_step' => 2]);
            $userData = (new UserTransformer)->transformUserDetail($user);
            return response()->json($userData, 200);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function updateAccount(Request $request)
    {
        try {
            $user = Auth::user();
            $userDetail = $user->userDetail;
            $user->update([
                'email' => $request->input('email', $user->email),
                'name' => $request->input('name', $user->name),
            ]);
            $userDetail->update([
                'timezone' => $request->input('timezone', $userDetail->timezone),
            ]);

            // add profile picture image
            if ($request->file('profile_picture')) {
                $this->updateImage($request, $userDetail, 'profile_picture');
            }
            $message = trans('common.account_update_success');
            $userData = (new UserTransformer)->transformUserDetail($user, $message);
            return response()->json($userData, 200);
        } catch (Exception $e) {
//            throw $e;
            return response()->json(['status' => 'error', 'message' => trans('common.app_error')], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function inviteEmail(Request $request)
    {
        try {
            $user = Auth::user();
            if($request->header('timezone')) {
                $user->userDetail->update(['timezone' => $request->header('timezone')]);
            } 
            $invites = $request->invites;
            if ($invites) {
   
                $user->userDetail->update(['invitee' => implode(',', $invites)]);
                $company = $user->company()->first();
                foreach($invites as $invite) {
                    $findUser = User::where('email', $invite)->first();
                    if(!$findUser){
                    $addUser = User::create(['email' => $invite, 'is_issue_invited' => 1]);
                    $addUser->userDetail()->create(['user_id' => $addUser->id]);
                    $addUser->company()->attach($company->id, ['role_id' => Role::$User]);
                    }
                }
                $sendInvitation = Notification::route('mail', $invites)->notify(new SendInviteEmail($user));
            }
            $result = $user->userDetail->update(['signup_step' => 3]);
            if ($result) {
                $message = ($invites) ? trans('common.invitation_send_success') : '';
                $token = $user->createToken('proflow')->plainTextToken;
                $userData = (new UserTransformer)->transformLogin($user, $token);
                return response()->json($userData, 200);
            }

          
        
            $token = $user->createToken('proflow')->plainTextToken;
            $userData = (new UserTransformer)->transformLogin($user, $token);
            return response()->json($userData, 200);
        } catch (Exception $e) {
            //throw $e;
            return response()->json(['status' => 'error', 'message' => trans('common.app_error')], 500);
        }
    }

    /**
     * @return Collection
     * @throws Exception
     */
    public function getTimezoneList()
    {
        $timezone = array();
        $timestamp = time();
        foreach (timezone_identifiers_list(DateTimeZone::ALL) as $key => $t) {
            date_default_timezone_set($t);
            $dateTime = new DateTime(now());
            $dateTime->setTimeZone(new DateTimeZone($t));
            $abbreviation = $dateTime->format('T');
            $timezone[$key]['zone'] = $t;
            $timezone[$key]['GMT_difference'] = date('P', $timestamp);
            $timezone[$key]['text'] = $abbreviation . ' ' . $t . '' . ' (GMT' . date('P', $timestamp) . ')';
        }
        $timezone = collect($timezone)->sortBy('GMT_difference')->pluck('text', 'zone');
        return $timezone;
    }

    public function updateOnboardingDetail(Request $request) {

        $user = Auth::user();
        $userDetail = $user->userDetail;
       
        if($request->on_boarding_step === 3 && $userDetail->on_boarding_step === 2)  {
            $userDetail->update([
                'on_boarding_step' => $request->input('on_boarding_step', $userDetail->on_boarding_step),
            ]);
        }else if($request->on_boarding_step !== 3){
        $userDetail->update([
            'on_boarding_step' => $request->input('on_boarding_step', $userDetail->on_boarding_step),
        ]);
        }
        $userData = (new UserTransformer)->transformUserDetail($user);
        return response()->json($userData, 200);
    }
}
