<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IssueStepUser extends Model
{
    protected $table = 'issue_step_user';


    public function issueStep()
    {
        return $this->hasOne('App\IssueStep','id','issue_step_id');
    }

}
