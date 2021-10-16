<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Company;
use App\Issue;
use App\IssueStep;
use App\Http\Transformer\IssueTransformer;
use App\Notifications\IssueStepAssigned;
use App\Notifications\IssueImmediateStepAssigned;
use App\User;
use Illuminate\Support\Facades\Notification;

class IssueStepController extends Controller
{
    /**
     * @var object
     */


  public $company;


  public function __construct()
  {
    $this->company = Company::where('workspace_url', request()->header('subdomain'))->first();
  }

  public function updateIssueStep(Request $request)
  {

    try {
      $issue = Issue::where('unique_id', $request->issue_id)->first();
      $dataId = $request->step_id;
      if ($dataId) {    
        if(isset($request->text)) {
          $issue->issueStep()->where('id', $request->step_id)->update(['text' => strip_tags($request->text)]);
        }
        if(isset($request->due_date)) {
          $issue->issueStep()->where('id', $request->step_id)->update(['due_date' => $request->due_date]);
        }
        if(isset($request->is_resolved)) {
          $issue->issueStep()->where('id', $request->step_id)->update(['is_resolved' => $request->is_resolved]);
        }
        $issueStep =  IssueStep::find($dataId);
        if($request->member_id && $request->member_id === 'unassigned') {
            $issueStep->update(['is_unassigned' => 1]);
            $issueStep->peopleAssignedToUserDetail()->detach();
        } else if($request->member_id && $request->member_id === 'assigned') {
          $issueStep->update(['is_unassigned' => 0]);
        }
        else if($request->member_id) {
            $issueStep->update(['is_unassigned' => 0]);
            $issueStep->peopleAssignedToUserDetail()->toggle([$request->member_id]);
            $issue->peopleFollowedUserDetail()->syncWithoutDetaching([$request->member_id => ['is_follower' => 1]]);
            $totalIssueSteps  = $issue->issueStep()->count();
            $issueStepsAssigned  = $issueStep->peopleAssignedToUserDetail()->where('user_id', $request->member_id)->count();
            $isImmediate = IssueStep::where('issue_id',$issue->id)->orderBy('id', 'desc')
            ->skip(1)->first();
            if($totalIssueSteps > 1 && $issueStepsAssigned === 1 && $isImmediate->is_resolved === 1){
              $sendUser = User::find($request->member_id);
              $sendUser->notify(new IssueImmediateStepAssigned($issue, $issueStep,  $this->company->workspace_url, $request->user()->name));
            } else if($issueStepsAssigned === 1){
              $sendUser = User::find($request->member_id);
              $sendUser->notify(new IssueStepAssigned($issue, $issueStep,  $this->company->workspace_url, $request->user()->name));
            }  
        }
      } else {
        $dataId = $issue->issueStep()->create(['is_resolved'=>0])->Increment('position_id');
      }
      
      $result = (new IssueTransformer)->transformIssueStepsData($issue->issueStepPosition);
      return response()->json($result, 200);
    } catch (\Exception $e) {
      throw $e;
      return response()->json(['status' => 'error', 'message' => trans('common.app_error')], 500);
    }
  }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */


    public function softdelete($id)
    {
        $issueSteps = IssueStep::findorfail($id); // fetch the note
        $issueSteps->delete();
        return response()->json(['status' => 'success'], 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
  

   public function savePositionData(Request $request)
  {
    $data = $request->all();
    $i = count($data) + 1;
    foreach ($data as $value) {
      $i--;
      $item = IssueStep::find($value['id']);
      $item->position_id = $i;
      $item->save();
    }

    return response()->json(['status' => 'success'], 200);
  }
}