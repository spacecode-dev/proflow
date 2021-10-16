<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use App\Traits\ObservantTrait;

class IssueComment extends Model
{
    use ObservantTrait;
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'body','company_id'
    ];

     //public $table='roles'; 
    

     public function commentable()
     {
         return $this->morphTo();
     }
 
     public function user()
     {
         return $this->belongsTo('App\User','created_by');
     }
   
     public function userDetail()
     {
         return $this->belongsTo('App\UserDetail','created_by','user_id');
     }
}
