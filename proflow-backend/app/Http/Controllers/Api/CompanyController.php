<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Company;
use App\Http\Transformer\UserTransformer;
use App\Http\Transformer\CompanyTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\ImageUpload;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendInviteEmail;
use App\Role;
use App\User;

class CompanyController extends Controller
{
    use ImageUpload;

    /**
     * @var object
     */
    public $company;

    /**
     * CompanyController constructor.
     */
    public function __construct()
    {
        $this->company = Company::where('workspace_url', request()->header('subdomain'))->first();
    }

    /**\
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $user = Auth::user();
            $validator = Validator::make($data, [
                'workspace_url' => [
                    'required', 'unique:companies,workspace_url,' . $user->id . ',created_by',
                    'not_in:app,staging,wp'
                ],
                'name' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()->first()], 422);
            }
            $data['created_by'] = Auth::id();
            $company = Company::updateOrCreate(['workspace_url' => $data['workspace_url']], $data);
            $wasRecentlyCreated = $company->wasRecentlyCreated;

            if ($wasRecentlyCreated) {
                $user->userDetail->update(['signup_step' => 1]);
                $user->company()->attach($company->id, ['role_id' => Role::$Admin]);
            }

            $userData = (new UserTransformer)->transformUserDetail($user);
            return response()->json($userData, 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => trans('common.app_error')], 500);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->all();
            $user = Auth::user();
            $company = Company::find($id);
            $company->update([
                'name' =>  $request->input('name', $company->name),
            ]);

            // add logo image
            if ($request->file('logo')) {
                $this->updateImage($request, $company, 'logo');
            }

            $message = trans('common.account_update_success');
            $userData = (new UserTransformer)->transformUserDetail($user, $message);
            return response()->json($userData, 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => trans('common.app_error')], 500);
        }
    }

    /**
     * @return JsonResponse
     */
    public function getMembersList()
    {
        try {
            $company = $this->company;
            $data = $company->users()
                ->get();
            $userData = (new CompanyTransformer)->transformMembersList($data);
            return response()->json($userData, 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => trans('common.app_error')], 500);
        }
    }

    public function getMentionsList()
    {
        try {
            $userId = Auth::id();
            $company = $this->company;
            $data = $company->users()
                ->get();
            $userData = (new CompanyTransformer)->transformMentionsList($data);
            return response()->json($userData, 200);
        } catch (Exception $e) {

            return response()->json(['status' => 'error', 'message' => trans('common.app_error')], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addMember(Request $request)
    {
        try {
            $company = $this->company;
            $data = $request->all();
            $user = Auth::user();
            $invites = $request->email;
            if ($invites) {
                $validator = Validator::make($data, [
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                ]);
                if ($validator->fails()) {
                    return response()->json(['message' => $validator->errors()->first()], 422);
                }
                $userDetail = $user->userDetail;
                if ($userDetail->invitee) {
                    $array = explode(',', $userDetail->invitee);
                    array_push($array, $invites);
                    $modifiedData =  implode(',', $array);
                } else {
                    $modifiedData = $invites;
                }
                $addUser = User::create(['email' => $invites, 'is_issue_invited' => 1]);
                $addUser->userDetail()->create(['user_id' => $addUser->id, 'invitee' => $modifiedData]);
                $addUser->company()->attach($company->id, ['role_id' => Role::$User]);
                Notification::route('mail', $invites)->notify(new SendInviteEmail($user));
            }
            $userData = ['message' =>  trans('common.add_member_success')];
            return response()->json($userData, 200);
        } catch (Exception $e) {
          //  throw $e;
            return response()->json(['status' => 'error', 'message' => trans('common.app_error')], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function changeMemberRole(Request $request)
    {
        try {
            $company = $this->company;
            $company->users()->updateExistingPivot($request->user_id, ['role_id' => $request->role_id]);
            $userData = ['message' =>  trans('common.change_member_success')];
            return response()->json($userData, 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => trans('common.app_error')], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function removeMember(Request $request)
    {
        try {
            $company = $this->company;
            $company->users()->detach($request->user_id);
            $userData = ['message' =>  trans('common.remove_member_success')];
            return response()->json($userData, 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => trans('common.app_error')], 500);
        }
    }
}
