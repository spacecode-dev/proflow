<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('auth/google', 'Auth\RegisterController@redirectToGoogle');
Route::get('auth/google/callback', 'Auth\RegisterController@handleGoogleCallback');
Route::get('/home', 'HomeController@index')->name('home');

Route::get('mailable/{issue}/{step}', function ($issue, $step) {
    $issue = App\Issue::where('unique_id', $issue)->first();
    $issueStep = App\IssueStep::find($step);

    $workspace_url = 'airbnb';
    $user_name = 'John Deo';

    return (new App\Notifications\issueImmediateStepAssigned($issue, $issueStep, $workspace_url, $user_name))
        ->toMail('jinal@mailinator.com');
});
