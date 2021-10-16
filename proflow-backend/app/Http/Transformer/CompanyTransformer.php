<?php

namespace App\Http\Transformer;

use URL;
use App\User;


class CompanyTransformer
{

  public function transformMembersList($data)
  {
  
    $var = [];
    $var = $data->map(function ($item) {
        return [
            'id' => $item->id ?? '',
            'name' => $item->name ?? '',
            'email' => $item->email ?? '',
            'profile_picture' => $item->userDetail->profile_picture ?? '',
            'role_id' => $item->pivot->role_id ?? '',
        ];
    });
    return $var;
  }

  public function transformMentionsList($data)
  {
  
    $var = [];
    $var = $data->map(function ($item) {
        return [
            'id' => $item->name ? '@'.$item->name :'@'.$item->email,
            'userId' => $item->id ?? '',
            'name' => $item->name ? $item->name : $item->email,
            'email' => $item->email ?? '',
            'profile_picture' => $item->userDetail->profile_picture ?? '',
            'role_id' => $item->pivot->role_id ?? '',
        ];
    });
    return $var;
  }
  
  

  public function transformGroupMembersList($data, $message = '')
  {

    $result = [
      'id' => $data->id ?? '',
      'name' => $data->name ?? '',
      'member_list' => $this->transformMembersList($data->member_list),
      'message' => $message
    ];

    return $result;

  }

  public function transformGroupList($data)
  {
    $result = [];
    $result = $data->map(function ($item) {
        return [
            'id' => $item->id ?? '',
            'name' => $item->name ?? '',
           
        ];
    });
    return $result;

  }

}
