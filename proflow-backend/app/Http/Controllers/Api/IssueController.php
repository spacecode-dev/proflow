<?php

namespace App\Http\Controllers\Api;

use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Transformer\IssueTransformer;
use App\Company;
use App\Issue;
use App\IssueComment;
use App\IssueStep;
use App\Tag;
use App\Group;
use App\IssueFile;
use App\Role;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Notifications\IssueStepAssigned;
use App\Traits\ImageUpload;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\IssueResolvedFollower;
use App\Notifications\IssueDueDateChanged;
use App\Notifications\IssueMentionedComment;


class IssueController extends Controller
{

    use ImageUpload;

    public $company;

    /**
     * IssueController constructor.
     */
    public function __construct()
    {
        $this->company = Company::where('workspace_url', request()->header('subdomain'))->first();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        try {
            $currentUserId = $request->user()->id;
            $data = $request->all();
            $issue = new Issue();
            $issue->unique_id = uniqid();
            $issue->fill($data);
            $issue->save();
            if ($request->issue_summary) {
                $issue->issueSummary()->createMany($request->issue_summary);
            }
            if ($request->issue_step) {
                $issue->issueStep()->createMany($request->issue_step);
            }
            $issue->peopleFollowedUserDetail()->syncWithoutDetaching([$currentUserId => ['is_follower' => 1]]);
            $issueData = (new IssueTransformer)->transformIssueCreateData($issue);
            return response()->json($issueData, 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => trans('common.app_error')], 500);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function update(Request $request, $id)
    {

        try {
            $data = $request->all();
            $issue = Issue::where('unique_id', $id)->first();
            unset($data['id']);
            $issue->update($data);

            $changes = $issue->getDirty();
            if ($issue->isDirty('due_date')) {
                // add the email for change date
                $users = $issue->peopleFollowedUserDetail()->get()->pluck('id')->merge($issue->created_by)
                    ->merge(IssueStep::where('issue_id', $issue->id)->with('peopleAssignedToUserDetail')->pluck('id'));
                $listUsers = User::whereIn('id', $users->unique()->toArray())->get();
                Notification::send($listUsers, new IssueDueDateChanged($issue, $this->company->workspace_url));
            }
            if ($request->summary) {
                $issue->issueSummary('summary')->update(['text' => $request->summary]);
            }
            $data = Issue::where('unique_id', $id)->with([
                'issueStep' => function ($query) {
                    $query->select('id', 'issue_id', 'text', 'position_id', 'due_date', 'is_resolved', 'is_unassigned')->orderBy('position_id', 'desc');
                },
                'issueStep.comments' => function ($query) {
                    $query->select('id', 'body', 'commentable_id', 'created_by', 'created_at')->get();
                },
                'issueSummary.comments' => function ($query) {
                    $query->select('id', 'body', 'commentable_id', 'created_by', 'created_at')->get();
                },
                'tags', 'peopleFollowedUserDetail.userDetail', 'peopleInvitedUserDetail.userDetail',

            ])->get()[0];
            $commentData = (new IssueTransformer)->transformIssueDetailData($data);
            return response()->json($commentData, 200);
        } catch (Exception $e) {
            throw $e;
//            return response()->json(['status' => 'error', 'message' => trans('common.app_error')], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateTag(Request $request)
    {
        $issue = Issue::where('unique_id', $request->id)->first();
        $data = $request->tag_name;
        if (isset($data['id'])) {
            $id = $data['id'];
        } else {
            $createTag = $this->company->tags()->create(['title' => $request->tag_name]);
            $id = $createTag->id;
        }
        $issue->tags()->attach($id);
        $data = $issue->tags()->get();
        return response()->json($data, 200);
    }

    /**
     * @return JsonResponse
     */
    public function getDefaultTag()
    {
        $data = Tag::where('company_id', 0)->orWhere('company_id', $this->company->id)->get();
        return response()->json($data, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteTag(Request $request)
    {
        $issue = Issue::where('unique_id', $request->id)->first();
        $issue->tags()->detach($request->tag_id);
        $data = $issue->tags()->get();
        return response()->json($data, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateMembers(Request $request)
    {
      try {
        $issue = Issue::where('unique_id', $request->issue_id)->first();
        if ($request->type === 'invited') {
            $issue->peopleInvitedUserDetail()->syncWithoutDetaching([$request->member_id => ['is_invited' => $request->is_invited]]);
            $result = (new IssueTransformer)->transformIssueFollowerData($issue->peopleInvitedUserDetail);
        } else if ($request->type === 'follower') {
            $issue->peopleFollowedUserDetail()->syncWithoutDetaching([$request->member_id => ['is_follower' => $request->is_follower]]);
            $result = (new IssueTransformer)->transformIssueFollowerData($issue->peopleFollowedUserDetail);
        } else if ($request->type === 'add-invite-email') {
            if ($issue->people_invite_email) {
                $array = explode(',', $issue->people_invite_email);
                array_push($array, $request->invite_email);
                $modifiedData = implode(',', $array);
            } else {
                $modifiedData = $request->invite_email;
            }
            $issue->update(['people_invite_email' => $modifiedData]);
            $user = User::create(['email' => $request->invite_email, 'is_issue_invited' => 1]);
            $user->userDetail()->create(['user_id' => $user->id]);
            $user->company()->attach($this->company->id, ['role_id' => Role::$User]);
            $issue->peopleInvitedUserDetail()->syncWithoutDetaching([$user->id => ['is_invited' => $request->is_invited]]);
            $result = (new IssueTransformer)->transformIssueFollowerData($issue->peopleInvitedUserDetail);
        }
        return response()->json($result, 200);
      }
      catch (Exception $e) {
        // throw $e;
        return response()->json(['status' => 'error', 'message' => 'Please add email'], 500);
    }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateIssueStatus(Request $request)
    {
        $issue = Issue::where('unique_id', $request->issue_id)->first();
        if ($request->has('is_resolved')) {
            $is_resolved = $request->get('is_resolved');
            $resolve_text = $request->get('resolve_text');
            if ($is_resolved === 1) {
                $users = $issue->peopleFollowedUserDetail()->get();
                Notification::send($users, new IssueResolvedFollower($issue, $this->company->workspace_url));
            }
            $issue->update(['is_resolved' => $is_resolved, 'resolve_text' => $resolve_text]);
            return response()->json($resolve_text, 200);
        } else if ($request->has('is_reopen')) {
            $issue->update(['is_resolved' => 0]);
            return response()->json($request->has('is_resolved'), 200);
        } else if ($request->has('is_delete')) {
            $issue->update(['is_archived' => $request->get('is_delete')]);
            return response()->json(['status' => 'success', 'message' => trans('common.issue_archieve')], 200);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function saveSummary(Request $request)
    {

        $issue = Issue::where('unique_id', $request->issue_id)->first();
        $issue->issueSummary()->where('id', $request->summary_id)->update(['text' => $request->text]);
        if ($request->mention_id) {
            $issue->peopleMentionedUserDetail()->syncWithoutDetaching([$request->mention_id => ['is_mention' => 1]]);
        }
        return response()->json($issue->issueSummary()->where('id', $request->summary_id)->get(), 200);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function saveFile(Request $request)
    {
        $data = $request->all();
        $issue = Issue::where('unique_id', $request->issue_id)->first();
        try {
            if ($request->file('file')) {
                $issueFile = $issue->IssueFile()->create($data);
                $this->updateIssueFileImage($request, $issueFile, 'file', 'issue_file');
            }

            $result = (new IssueTransformer)->transformFileData($issue->issueFile);
            return response()->json($result, 200);
        } catch (Exception $e) {
            // throw $e;
            return response()->json(['status' => 'error', 'message' => trans('common.app_error')], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function updateFile(Request $request)
    {
        try {
            $issueFile = IssueFile::where('id', $request->id)->first();
            if ($request->file('file')) {
                $this->updateIssueFileImage($request, $issueFile, 'file', 'issue_file');
            }
            if ($request->name) {
                $issueFile->update(['name' => $request->name]);
            }

            $issue = Issue::where('id', $issueFile->issue_id)->first();
            $result = (new IssueTransformer)->transformFileData($issue->issueFile);
            return response()->json($result, 200);
        } catch (Exception $e) {
            // throw $e;
            return response()->json(['status' => 'error', 'message' => trans('common.app_error')], 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteFile(Request $request)
    {
        $issue = Issue::where('unique_id', $request->issue_id)->first();
        $issueFile = IssueFile::find($request->id);
        $this->deleteIssueFileImage('issue_file', $issueFile->file);
        $issueFile->delete();
        $result = (new IssueTransformer)->transformFileData($issue->issueFile);
        return response()->json($result, 200);
    }

     /**
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteIssue(Request $request)
    {
        try {
        $issue = Issue::where('unique_id', $request->issue_id)->first();
        $issue->issueStep()->forceDelete();
        $issue->comments()->forceDelete();
        $issue->issueSummary()->forceDelete();
        $issue->tags()->forceDelete();
        $issue->peopleInvolved()->detach();
        $issue->forceDelete();
        return response()->json(['status' => 'success', 'message' => 'Issue deleted successfully'], 200);
        } catch (Exception $e) {
            // throw $e;
            return response()->json(['status' => 'error', 'message' => trans('common.app_error')], 500);
        }
    }

    /**
     * @return JsonResponse
     */
    public function getTags()
    {
        return response()->json(Tag::get(['id', 'title']), 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getIssues(Request $request)
    {
        $type = $request->get('type');
        $user = $request->user();
        $userId = $user->id;
        $companyId = $request->get('companyId');
        $skip = $request->get('skip');
        $take = $request->get('take');
        $issues = collect([]);
        $with = ['assignedIssueSteps' => function ($query) use ($userId) {
            $query->where('is_resolved', 0)->where('user_id', $userId)->get();
        }, 'nextStep', 'tags', 'peopleInvolved'];
        $get = ['id', 'title', 'priority', 'visibility', 'due_date', 'created_at', 'unique_id'];
        if (!$type) {
            $mentionIssue = $user->mentionIssue()->where(['is_resolved' => 0, 'is_archived' => 0]);
            $mention = $mentionIssue->count() ? $mentionIssue->get()->pluck('id') : collect([]);
            $invitedIssue = $user->invitedIssue()->where(['is_resolved' => 0, 'is_archived' => 0]);
            $invited = $invitedIssue->count() ? $invitedIssue->get()->pluck('id') : collect([]);
            $followerIssue = $user->followerIssue()->where(['is_resolved' => 0, 'is_archived' => 0]);
            $follower = $followerIssue->count() ? $followerIssue->get()->pluck('id') : collect([]);
            $createdIssue = Issue::where(['created_by' => $userId, 'visibility' => 'company', 'company_id' => $companyId, 'is_resolved' => 0, 'is_archived' => 0]);
            $created = $createdIssue->count() ? $createdIssue->get()->pluck('id') : collect([]);
            $issues = Issue::whereIn('id', $created->merge([$mention, $invited, $follower])->flatten()->unique()->toArray());
        } else if ($type === 'company') {
            $issues = Issue::where(['created_by' => $userId, 'visibility' => 'company', 'company_id' => $companyId]);
        } else if ($type === 'resolved') {
            $issues = Issue::where(['created_by' => $userId, 'company_id' => $companyId, 'is_resolved' => 1]);
        } else if ($type === 'drafts') {
            $issues = Issue::where(['created_by' => $userId, 'visibility' => 'draft', 'company_id' => $companyId]);
        } else if ($type === 'private') {
            $followerIssue = $user->followerIssue()->where('visibility', 'private');
            $follower = $followerIssue->count() ? $followerIssue->get()->pluck('id') : collect([]);
            $createdIssue = Issue::where(['created_by' => $userId, 'visibility' => 'private', 'company_id' => $companyId]);
            $created = $createdIssue->count() ? $createdIssue->get()->pluck('id') : collect([]);
            $issues = Issue::whereIn('id', $created->merge([$follower])->flatten()->unique()->toArray());
        } else if ($type === 'unassigned') {
            $ids = Issue::where(['created_by' => $userId, 'is_resolved' => 0, 'is_archived' => 0, 'company_id' => $companyId])->pluck('id');
            foreach ($ids as $key => $value) {
                $is_unassigned = IssueStep::where(['issue_id' => $value, 'is_unassigned' => 1])->count();
                if (!$is_unassigned) {
                    $ids->pull($key);
                }
            }
            $issues = Issue::whereIn('id', $ids->toArray());
            $with = ['unassignedNextSteps', 'tags', 'peopleInvolved'];
        } else if ($type === 'group') {
            $group = Group::where('id', intval($request->get('groupId')))->first();
            $membersArray = [];
            $issuesAssignedToStepsIds = collect([]);
            if($group && count($group->member_list) > 0) {
                $membersArray = $group->member_list->pluck('id');
                foreach ($group->member_list as $member) {
                    $issuesAssignedToStepsIds = $issuesAssignedToStepsIds->merge($member->issueStep->pluck('id'))->unique();
                }
            }
            $membersDraftIssues = Issue::whereIn('created_by', $membersArray)->where('visibility', 'draft');
            $d = $membersDraftIssues->count() ? $membersDraftIssues->get()->pluck('id') : collect([]);

            $createdByMembers = IssueStep::whereIn('created_by', $membersArray);
            $c = $createdByMembers->count() ? $createdByMembers->get()->pluck('issue_id') : collect([]);

            $assignedToStepsIds = IssueStep::whereIn('id', $issuesAssignedToStepsIds);
            $a = $assignedToStepsIds->count() ? $assignedToStepsIds->get()->pluck('issue_id') : collect([]);

            $issues = Issue::whereIn('id', collect([])->merge([$d, $c, $a])->flatten()->unique()->toArray());
        } else if ($type === 'archived') {
            $issues = Issue::where(['created_by' => $userId, 'company_id' => $companyId, 'is_archived' => 1]);
        }
        $count = $issues->count();
        $resolve = $issues->skip($skip)->take($take);
        return response()->json([
            'issues' => $count ? $resolve->with($with)->get($get) : [],
            'issuesCount' => (object)[
                'all' => $count,
                'show' => $count ? $skip + $resolve->get('id')->count() : 0
            ]
        ], 200);
    }

//    /**
//     * @param Request $request
//     * @return JsonResponse
//     */
//    public function getDraftIssues(Request $request)
//    {
//        $drafts = Issue::where(['created_by' => $request->user()->id, 'visibility' => 'draft'])->orderBy('created_at', 'desc')->limit(5)->get(['unique_id', 'title']);
//        return response()->json($drafts, 200);
//    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getIssuesCount(Request $request)
    {
        return response()->json((new IssueTransformer)->transformIssueCounts($request->user(), $request->get('companyId'), $request->get('groupIds')), 200);
    }

    /**
     * @param Request $request
     */
    public function saveMention(Request $request)
    {
        $issue = Issue::where('unique_id', $request->issue_id)->first();
        if ($issue) {
            $issue->peopleMentionedUserDetail()->syncWithoutDetaching([$request->mention_id => ['is_mention' => 1]]);
            //   $sendUser = User::find($request->mention_id);
            //   $sendUser->notify(new IssueMentionedComment($issue, '', $this->company->workspace_url, $request->user()->name));
        }
    }

    /**
     * @param Request $request
     */
    public function nextStepDue(Request $request)
    {
        $issueLists = Issue::where('is_resolved', 0)->whereHas(['issueStep' => function ($query) {
            $query->whereDate('due_date', '<=', Carbon::now()->addDays(1)->toDateTimeString())
                ->orwhereDate('due_date', '=', Carbon::now()->addDays(2)->toDateTimeString())->get();
        }])->get();
        //  foreach($issueLists as $issueList) {

        //     $issueStep = $issueList->with(['issueStep' => function ($query) {
        //       $query->whereDate('due_date', '=', Carbon::now()->addDays(1)->toDateTimeString())
        //         ->orwhereDate('due_date', '=', Carbon::now()->addDays(2)->toDateTimeString())->get();
        //     }])->get();
        print_R($issueLists);

        // $sendUser = User::find($request->mention_id);
        // $sendUser->notify(new IssueMentionedComment($issue, '', $this->company->workspace_url, $request->user()->name));


        //  $issue->peopleMentionedUserDetail()->syncWithoutDetaching([$request->mention_id => ['is_mention' => 1]]);
        //  $sendUser = User::find($request->mention_id);
        //  $sendUser->notify(new IssueMentionedComment($issue, '', $this->company->workspace_url, $request->user()->name));


    }

    public function nextStepDueOverDue()
    {

    }

}
