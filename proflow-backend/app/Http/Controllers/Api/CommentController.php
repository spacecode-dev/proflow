<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Company;
use App\Issue;
use App\User;
use App\Http\Transformer\IssueTransformer;
use Exception;
use Illuminate\Support\Facades\Notification;
use App\Notifications\IssueMentionedComment;


class CommentController extends Controller
{

    public $company;

    public function __construct()
    {
        $this->company = Company::where('workspace_url', request()->header('subdomain'))->first();
    }

    public function store(Request $request)
    {
        try {
            $issue = Issue::where('unique_id', $request->issue_id)->first();
            if ($request->body) {
                // $string = htmlentities($request->body, null, 'utf-8');
                $content = str_replace("&nbsp;", "", $request->body);
                // $content = html_entity_decode($request->body);
                if ($request->type == 'steps') {
                    $issueStep = $issue->issueStep()->where('id', $request->id)->first();
                    $issueStep->comments()->create(['body' => ($content)]);
                    $issueData = $issueStep->comments;
                } else if ($request->type == 'summary') {
                    $issueSummary = $issue->issueSummary()->where('id', $request->id)->first();
                    $issueSummary->comments()->create(['body' => ($content)]);
                    $issueData = $issueSummary->comments;
                } else {
                   
                    $issue->comments()->create(['body' => ($content)]);
                    $issueData = $issue->comments;
                }

                if($request->mention_id) {
                    $issue->peopleMentionedUserDetail()->syncWithoutDetaching([$request->mention_id => ['is_mention' => 1]]);
                    $users=User::find($request->mention_id);
                    Notification::send($users, new IssueMentionedComment($issue, $this->company->workspace_url, $request->body, $request->user()->name));    
                }
            } else {
                $issueData = $issue->comments;
            }

            $commentData = (new IssueTransformer)->transformIssueCommentData($issueData);
            return response()->json($commentData, 200);
        } catch (Exception $e) {
            throw $e;
            return response()->json(['status' => 'error', 'message' => trans('common.app_error')], 500);
        }
    }

    public function getComments($issue_id)
    {
        try {
            $data = Issue::where('unique_id', $issue_id)->with('issueStep.comments')->get();
            $commentData = (new IssueTransformer)->transformIssueCommentData($data);
            return response()->json($data, 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => trans('common.app_error')], 500);
        }
    }
}
