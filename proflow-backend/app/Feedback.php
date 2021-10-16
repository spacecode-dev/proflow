<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use App\Traits\ObservantTrait;


class Feedback extends Model
{
    use ObservantTrait;
    protected $table= 'feedbacks';
    
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'text', 'company_id', 'created_by'
    ];
    

   
}
