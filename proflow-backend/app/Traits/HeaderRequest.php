<?php

namespace App\Traits;

use URL;
use App\Company;
use App\Role;
use config;

trait HeaderRequest {

    //api image
    public function updateHeaderInfo($timezone, $subdomain = '',$user ='') {

        if($timezone) {
            $user->userDetail->update(['timezone' => $timezone]);
        } 
        
        if($subdomain && $subdomain !== config('app.frontend_stagsing_domain')) {
              $company = Company::where('workspace_url',$subdomain)->first();
            if($company && $user->id !== $company->created_by && $user->userDetail->signup_step !== 3) {
              $user->userDetail->update(['invited_by'=>  $company->created_by,'signup_step'=>1]); 
              $companyUserExist = $user->company()->where('company_id',$company->id)->first();
              if(!$companyUserExist) {
                $user->company()->syncWithoutDetaching([$company->id => ['role_id' => Role::$User]]);
              }
            
            }
        }
       
    }

  
}


