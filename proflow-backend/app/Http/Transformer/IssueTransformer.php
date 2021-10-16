<?php

namespace App\Http\Transformer;


use App\Group;
use App\Issue;
use App\IssueStep;
use App\User;

class IssueTransformer
{
    /**
     * @param $issueData
     * @return array
     */
    public function transformIssueCreateData($issueData)
    {
        return [
            'title' => $issueData->title,
            'due_date' => $issueData->due_date ?? '',
            'visibility' => $issueData->visibility ?? '',
            'priority' => $issueData->priority ?? '',
            'issue_summary' => $issueData->issueSummary ?? '',
            'unique_id' => $issueData->unique_id ?? '',
            'issue_step' => $issueData->issueStep ?? '',
        ];
    }

    /**
     * @param $issueData
     * @return array
     */
    public function transformIssueDetailData($issueData)
    {
        $issueStep = ($issueData->issueStep) ? $issueData->issueStep->map(function ($item) {
            return [
                'due_date' => $item->due_date,
                'id' => $item->id,
                'is_resolved' => $item->is_resolved,
                'issue_id' => $item->issue_id,
                'position_id' => $item->position_id,
                'text' => $item->text,
                'is_unassigned' => $item->is_unassigned,
                'member_list' => $this->transformIssueFollowerData($item->peopleAssignedToUserDetail) ?? [],
                'comments' => $this->transformIssueCommentData($item->comments) ?? [],
            ];
        }) : [];
        $issueSummary = ($issueData->issueSummary) ? $issueData->issueSummary->map(function ($item) {
            return [
                'text' => $item->text,
                'id' => $item->id,
                'type' => $item->type,
                'issue_id' => $item->issue_id,
                'comments' => $this->transformIssueCommentData($item->comments) ?? [],
            ];
        }) : [];
        return [
            'title' => $issueData->title ?? '',
            'issue_file' => $this->transformFileData($issueData->issueFile) ?? [],
            'additional_info' => $issueData->additional_info ?? '',
            'company_id' => $issueData->company_id,
            'resolve_text' => $issueData->resolve_text ?? '',
            'due_date' => $issueData->due_date,
            'visibility' => $issueData->visibility,
            'priority' => $issueData->priority,
            'issue_summary' => $issueSummary,
            'unique_id' => $issueData->unique_id,
            'issue_step' => $issueStep,
            'people_invited_user_detail' => $this->transformIssueFollowerData($issueData->peopleInvitedUserDetail) ?? [],
            'people_follow_user_detail' => $this->transformIssueFollowerData($issueData->peopleFollowedUserDetail) ?? [],
            'tags' => $issueData->tags,
            'is_resolved' => $issueData->is_resolved,
            'is_archived' => $issueData->is_archived,
            'created_by_name' => $issueData->userCreatedBy->name ?? '',
            'created_by_email' => $issueData->userCreatedBy->email ?? '',
            'created_by_profile_pic' => $issueData->userCreatedBy->userDetail->profile_picture ?? '',
        ];
    }

    /**
     * @param $data
     * @return mixed
     */
    public function transformIssueStepsData($data)
    {
        $result = [];
        $result = $data->map(function ($item) {
            return [
                'due_date' => $item->due_date,
                'id' => $item->id,
                'is_resolved' => $item->is_resolved,
                'issue_id' => $item->issue_id,
                'position_id' => $item->position_id,
                'text' => $item->text,
                'is_unassigned' => $item->is_unassigned,
                'member_list' => $this->transformIssueFollowerData($item->peopleAssignedToUserDetail) ?? [],
                'comments' => $this->transformIssueCommentData($item->comments) ?? [],
            ];
        });
        return $result;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function transformIssueFollowerData($data)
    {
        $result = [];
        $result = $data->map(function ($item) {
            return [
                'id' => $item->id ?? '',
                'email' => $item->email ?? '',
                'profile_picture' => $item->userDetail->profile_picture ?? '',
                'name' => $item->name ?? '',
            ];
        });
        return $result;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function transformFileData($data)
    {

        $result = [];
        $result = $data->map(function ($item) {
            return [
                'id' => $item->id ?? '',
                'name' => $item->name ?? '',
                'file' => $item->file ?? '',
                'type' => $item->type ?? '',
                'url' => $item->url ?? '',
                'uploaded_by' => $item->user->name ?? '',
            ];
        });
        return $result;
    }

    /**
     * @param $issueCommentData
     * @return mixed
     */
    public function transformIssueCommentData($issueCommentData)
    {
        $result = [];
        $result = $issueCommentData->map(function ($item) {
            return [
                'id' => $item->id ?? '',
                'body' => $item->body ?? '',
                'profile_picture' => $item->user->userDetail->profile_picture ?? '',
                'name' => $item->user->name ?? '',
                'email' => $item->user->email ?? '',
                'created_at' => $item->created_at ?? '',
            ];
        });
        return $result;
    }

    /**
     * @param $user
     * @param $companyId
     * @param $groupIds
     * @return mixed
     */
    public function transformIssueCounts($user, $companyId, $groupIds)
    {
        return (object)[
            'my_issues' => $this->getMyIssuesCount($user, $companyId),
            'private' => $this->getPrivateCount($user, $companyId),
            'groups' => $this->getGroupsCount($groupIds),
            'company' => Issue::where(['created_by' => $user->id, 'visibility' => 'company', 'company_id' => $companyId])->count(),
            'unassigned' => $this->getUnassignedCount($user, $companyId),
            'drafts' => Issue::where(['created_by' => $user->id, 'visibility' => 'draft', 'company_id' => $companyId])->count(),
        ];
    }

    /**
     * @param $user
     * @param $companyId
     * @return int
     */
    public function getMyIssuesCount($user, $companyId)
    {
        $mentionIssue = $user->mentionIssue()->where(['is_resolved' => 0, 'is_archived' => 0]);
        $mention = $mentionIssue->count() ? $mentionIssue->get()->pluck('id') : collect([]);
        $invitedIssue = $user->invitedIssue()->where(['is_resolved' => 0, 'is_archived' => 0]);
        $invited = $invitedIssue->count() ? $invitedIssue->get()->pluck('id') : collect([]);
        $followerIssue = $user->followerIssue()->where(['is_resolved' => 0, 'is_archived' => 0]);
        $follower = $followerIssue->count() ? $followerIssue->get()->pluck('id') : collect([]);
        $createdIssue = Issue::where(['created_by' => $user->id, 'visibility' => 'company', 'company_id' => $companyId, 'is_resolved' => 0, 'is_archived' => 0]);
        $created = $createdIssue->count() ? $createdIssue->get()->pluck('id') : collect([]);
        return $created->merge([$mention, $invited, $follower])->flatten()->unique()->count();
    }

    /**
     * @param $user
     * @param $companyId
     * @return int
     */
    public function getPrivateCount($user, $companyId)
    {
        $invitedIssue = $user->invitedIssue()->where('visibility', 'private');
        $invited = $invitedIssue->count() ? $invitedIssue->get()->pluck('id') : collect([]);
        $createdIssue = Issue::where(['created_by' => $user->id, 'visibility' => 'private', 'company_id' => $companyId]);
        $created = $createdIssue->count() ? $createdIssue->get()->pluck('id') : collect([]);
        return $created->merge([$invited])->flatten()->unique()->count();
    }

    /**
     * @param $user
     * @param $companyId
     * @return int
     */
    public function getUnassignedCount($user, $companyId)
    {
        $ids = Issue::where(['created_by' => $user->id, 'is_resolved' => 0, 'is_archived' => 0, 'company_id' => $companyId])->pluck('id');
        foreach ($ids as $key => $value) {
            $is_unassigned = IssueStep::where(['issue_id' => $value, 'is_unassigned' => 1])->count();
            if (!$is_unassigned) {
                $ids->pull($key);
            }
        }
        return $ids->count();
    }

    /**
     * @param $groupIds
     * @return array
     */
    public function getGroupsCount($groupIds)
    {
        $groups = Group::whereIn('id', $groupIds)->get();
        $counts = [];
        foreach ($groups as $group) {
            $membersArray = [];
            $issuesAssignedToStepsIds = collect([]);
            if(count($group->member_list) > 0) {
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
            $counts[] = (object)[
                'id' => $group->id,
                'count' => collect([])->merge([$d, $c, $a])->flatten()->unique()->count()
            ];
        }
        return $counts;
    }
}
