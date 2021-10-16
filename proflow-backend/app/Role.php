<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    

    public static $SuperAdmin = 1;
    public static $Admin = 2;
    public static $User = 3;
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

     //public $table='roles'; 
    

    public function users()
    {
        return $this->belongsToOne('App\User','id');
    }
   
}
