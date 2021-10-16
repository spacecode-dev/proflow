<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Transformer\CompanyTransformer;
use App\Group;
use App\Company;

class GroupController extends Controller
{

    public $company;

    public function __construct()
    {
        $this->company = Company::where('workspace_url', request()->header('subdomain'))->first();
    }

    /**
     * @return JsonResponse
     */
    public function getGroups()
    {
        try {
            $allGroup = Group::where('company_id', $this->company->id)->get();
            $groupData = (new CompanyTransformer)->transformGroupList($allGroup);
            return response()->json($groupData, 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => trans('common.app_error')], 500);
        }
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        try {

            $updateGroup = Group::find($request->id);
            if($updateGroup){
               $updateGroup->update($request->all());
            }else{
               $updateGroup = Group::create($request->all());
            }
            $message = trans('common.group_update_success');
            $groupData = (new CompanyTransformer)->transformGroupMembersList($updateGroup, $message);
            return response()->json($groupData, 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => trans('common.app_error')], 500);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function edit($id)
    {
        try {
            $checkCompanyGroup = $this->company->groups()->where('id', $id)->first();
            if ($checkCompanyGroup) {
                $groupData = (new CompanyTransformer)->transformGroupMembersList($checkCompanyGroup);
                return response()->json($groupData, 200);
            }
            return response()->json(['status' => 'error', 'message' => trans('common.not_allowed_group')], 403);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => trans('common.app_error')], 500);
        }
    }
}
