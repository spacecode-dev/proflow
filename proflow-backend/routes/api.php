<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// guest routes api
Route::middleware(['api'])->prefix('v1')->namespace('Api')->group(function () {
    Auth::routes(['verify' => true]);
    Route::post('google-auth', 'Auth\RegisterController@googleAuth');
    Route::get('auth/google', 'Auth\RegisterController@redirectToGoogle');
    Route::get('auth/google/callback', 'Auth\RegisterController@handleGoogleCallback')->name('auth.callback');;
});


// authenticated routes api
Route::middleware(['auth:sanctum', 'verified'])->prefix('v1')->namespace('Api')->group(function () {
    //  Route::get('user', 'Auth\LoginController@me');

    Route::get('get-profile', 'UserDetailController@getProfile');
    Route::resource('company', 'CompanyController');
    Route::post('update-user', 'UserDetailController@profileDetail');
    Route::post('update-account', 'UserDetailController@updateAccount');
    Route::post('invite-email', 'UserDetailController@inviteEmail');
    Route::post('update-password', 'Auth\ResetPasswordController@updatePasssword');
    Route::get('get-timezone', 'UserDetailController@getTimezoneList');
    Route::get('mail', 'HomeController@mandrill');
    Route::get('get-members', 'CompanyController@getMembersList');
    Route::post('add-member', 'CompanyController@addMember');
    Route::post('change-member-role', 'CompanyController@changeMemberRole');
    Route::get('get-company-members', 'GroupController@getCompanyMembers');
    Route::post('remove-member', 'CompanyController@removeMember');
    Route::get('edit-group/{id}', 'GroupController@edit');
    Route::post('update-group', 'GroupController@update');
    Route::get('get-company-group', 'GroupController@getGroups');
    Route::get('remove-member', 'GroupController@removeMember');
    Route::post('update-onboarding', 'UserDetailController@updateOnboardingDetail');

    //issue detail api
    Route::post('update-issue/{id}', 'IssueController@update');
    Route::post('create-issue', 'IssueController@create');
    Route::post('update-issue-status', 'IssueController@updateIssueStatus');
    Route::post('create-comment', 'CommentController@store');
    Route::post('delete-issue', 'IssueController@deleteIssue');
    

    //invite user follower
    Route::post('add-members', 'IssueController@updateMembers');

    //mention list
    Route::get('mentions-list', 'CompanyController@getMentionsList');

    //save summary
    Route::post('add-summary', 'IssueController@saveSummary');

    // save files
    Route::post('save-file', 'IssueController@saveFile');
    Route::post('update-file', 'IssueController@updateFile');
    Route::post('delete-file', 'IssueController@deleteFile');

    //invite user
    Route::post('add-invited-members', 'IssueController@addInvitedPeople');

    //delete Tag
    Route::get('get-default-tag', 'IssueController@getDefaultTag');
    Route::post('update-tag', 'IssueController@updateTag');
    Route::post('delete-tag', 'IssueController@deleteTag');

    //issue steps
    Route::post('update-issue-step', 'IssueStepController@updateIssueStep');
    Route::delete('delete-issue-step/{id}', 'IssueStepController@softdelete');
    Route::post('update-step-position', 'IssueStepController@savePositionData');
    Route::post('get-comments/{issue_id}', 'CommentController@getComments');

    Route::post('save-mention', 'IssueController@saveMention');

    //issue list api
    Route::get('get-tags', 'IssueController@getTags');
    Route::post('get-issues', 'IssueController@getIssues');
//    Route::get('get-draft-issues', 'IssueController@getDraftIssues');
    Route::post('get-issues-count', 'IssueController@getIssuesCount');
    Route::post('send-feedback', 'HomeController@sendFeedback');

});
