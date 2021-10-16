<?php

namespace App\Http\Transformer;

use URL;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class UserTransformer
{



  public function transform(User $user)
  {
    return [
      'name' => $user->name,
    ];
  }

  public function transformLogin($user, $token, $message = '')
  {
    $company =  $user->company()->orderBy('companies.created_at', 'desc')->first();
    $data = [
      'name' => $user->name,
      'email' => $user->email,
      'department_id' => $user->department_id,
      'user_detail' => $user->userDetail,
      'company_detail' =>  $company,
      'invitee' => $user->invitee,
      'email_verified_at' => $user->email_verified_at,
      'token' => $token,
      'role_id' => $company->pivot->role_id ?? '',
      'groups' => ($company)?$company->groups()->select('id','name')->get():'',
    ];
    if ($message) {
      $data['message'] = $message;
    }
    return $data;
  }


  public function transformUserDetail($user, $message = '')
  {

    $company =  $user->company()->orderBy('companies.created_at', 'desc')->first();

    $data = [
      'name' => $user->name,
      'email' => $user->email,
      'user_detail' => $user->userDetail,
      'company_detail' =>  $company,
      'invitee' => $user->invitee,
      'email_verified_at' => $user->email_verified_at,
      'role_id' => $company->pivot->role_id ?? '',
      'groups' =>  ($company)?$company->groups()->select('id','name')->get():''
    ];

    if ($message) {
      $data['message'] = $message;
    }
    return $data;
  }



}
