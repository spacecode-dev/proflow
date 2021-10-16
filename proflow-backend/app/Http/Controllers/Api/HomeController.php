<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Mail;
use App\Http\Controllers\Controller;
use App\Company;

class HomeController extends Controller
{
    public $company;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('verified');
        if(request()->header('subdomain')){
            $this->company = Company::where('workspace_url', request()->header('subdomain'))->first();
        }  
      
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function mandrill()
    {
        Mail::send('emails.test', [], function($message)
        {
            $message->to('er.jinalkamdar@gmail.com')
                      ->from('harry@getproflow.com')
                             ->subject('Welcome to Laravel');
        });
    }


    /**
    * @param Request $request
    * @return JsonResponse
    */
   public function sendFeedback(Request $request) {
     $this->company->feedbacks()->create($request->all()); 
     return response()->json(['status' => 'success', 'message' => trans('common.send_feedback_success')], 200);
   }

}
